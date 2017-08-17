<?php
namespace Voilab\Restanswer;

use Voilab\Serviceanswer\Interfaces\Returnable;

class Processor {

    public $container;

    /**
     * Mapping between the expected return and the provided Returnable
     * @var array
     */
    public $mapping;

    /**
     * Is the provided Returnable a collection
     * @var boolean
     */
    public $isCollection;



    /** ================== Constructor ========================================== */

    public function __construct(Container $c) {
        $this->container = $c;
    }

    /** ================ / Constructors ========================================= */







    /** ================== Public methods ======================================= */

    /**
     * Process un retour de service et appel le moteur de rendu REST.
     *
     * @param Returnable $returnable Objet retourné par un service
     * @param string $format Un format de retour HTTP qui supplantera celui défini par défaut.
     * @param bool $interrupt Si vrai, interrompra le déroulement du workflow REST pour renvoyer directement la réponse
     * @return bool
     */
    public function process(Returnable $returnable, $format = null, $interrupt = false) {
        if (!$returnable->isSuccess()) {
            $http_status = 400;
            if (isset($this->container['config']['codeTranslator'][$returnable->getErrorCode()])) {
                $http_status = $this->container['config']['codeTranslator'][$returnable->getErrorCode()];
            }

            $this->container['response']
                ->setInterrupt($interrupt)
                ->setHttpStatus($http_status)
                ->setContent($returnable->getMessage())
                ->getRenderer($format)
                ->render();

            return true;
        }

        $response = $this->getProcessedResponse($returnable);
        $response->getRenderer($format)->render();
        return true;
    }

    /**
     * Récupération d'un objet Response avec le contenu mappé
     *
     * @param Returnable $returnable
     * @return Response
     */
    public function getProcessedResponse(Returnable $returnable) {
        $content = $returnable->getBody();

        // s'il y a un mapping à effectuer, on l'effectue
        if ($this->mapping) {
            $content = $this->recursiveMap($content, $this->mapping);
        }

        $response = $this->container['response']
            ->setHeaders($returnable->getMetadatas());
        if (!$returnable->isEmpty()) {
            $response->setContent($content);
        } else {
            $response->setHttpStatus(204);
        }

        return $response;
    }

    /**
     *  Configuration du map des données.
     *
     *  [
     *      'isCollection' => true,         // Le contenu est une collection d'objets, le mapping est appliqué à chacun.
     *      'mapping' => [
     *          'id' => 'id',               // Propriété 'id' d'un objet ou tableau
     *          'name' => 'getName',        // Appel de la fonction getName() sur l'objet
     *          'Owner' => [                // Nouveau mapping simple. L'objet est traité comme unique (Pas collection)
     *              'id' => 'id'
     *          ],
     *          'Products' => [             // Nouveau mapping complet. Permet de mapper une sous collection
     *              'isCollection' => true,
     *              'accessor' => 'getProducts', // Chemin ou fonction pour accèder à 'Products'
     *              'mapping' => [
     *                  'id' => 'id',
     *                  'label' => 'label'
     *              ]
     *          ],
     *          'User' => [
     *              '__name__' => 'Customer', // technique pour changer la clé sur une relation
     *              'id' => 'id'
     *          ]
     *      ]
     *  ]
     *
     *  @param  array  $mapping
     *
     *  @return Processor
     */
    public function map(array $mapping) {
        $this->mapping = $mapping;
        return $this;
    }

    /** ================ / Public methods ======================================= */

    /**
     *  Map recrusif des données.
     *
     *  @see Processor::map()
     *
     *  @param  Mixed  $content Objet de données
     *  @param  array  $mapping Configuration du mapping
     *
     *  @return array          Données mapées
     */
    private function recursiveMap($content, array $mapping) {
        $isCollection = isset($mapping['isCollection']) ? $mapping['isCollection'] : false;
        $mapping = isset($mapping['mapping']) ? $mapping['mapping'] : $mapping;
        $mapped = array();

        if ($isCollection) {
            // Mapping d'une collection, traite les éléments un par un.
            if (is_array($content) || $content instanceof \Traversable) {
                foreach ( $content as &$value ) {
                    $mapped[] = $this->recursiveMap( $value, $mapping );
                }
            }
        } elseif ($content) {
            // Mapping d'un élément unique.
            foreach ($mapping as $key => $map) {
                if (is_string($map)) {
                    // Map simple, valeur directe ou fonction. Surchargé par l'accessor si défini.
                    $mapped[$key] = $this->getKeyContent($content, $map);
                } else if (is_callable($map)) {
                    // Fonction de map avancé. Appel avec l'objet en paramètre et attribue le retour.
                    $mapped[$key] = $map($content);
                } else if (is_array($map)) {
                    // Nouveau mapping, récurssion.
                    $accessor = isset($map['accessor']) ? $map['accessor'] : $key;
                    if (isset($map['__name__']) && $map['__name__']) {
                        $key = $map['__name__'];
                    }

                    $mapped[$key] = $this->recursiveMap(
                        $this->getKeyContent($content, $accessor),
                        $map
                    );
                }
            }
        } else {
            $mapped = null;
        }

        return $mapped;
    }

    /**
     *  Pour un objet donné, va lire la propriété défini par le chemin d'accès.
     *  Types de valeurs pour accessor:
     *  - Index de tableau
     *  - Nom de propriété d'un objet
     *  - Méthode à appeler sur un objet
     *  - ??? (Truc avec processorMapping dans la config)
     *
     *  @param  Mixed  $object   Objet source des données
     *  @param  String $accessor Spécification de l'accesseur. Voir détails.
     *
     *  @return Mixed             Valeur ou null
     */
    private function getKeyContent($object, $accessor) {
        $successive_acessors = explode('.', $accessor);
        foreach ($successive_acessors as $accessor) {
            if (is_array($object)) {
                // Index de tableau
                if (isset($object[$accessor])) {
                    $object = $object[$accessor];
                } else {
                    return null;
                }
            } elseif (isset($object->$accessor)) {
                // Nom de propriété d'un objet
                $object = $object->$accessor;
            } elseif (method_exists($object, $accessor)) {
                // Méthode à appeler sur un objet
                $object = $object->$accessor();
            } elseif ($this->container['config']['processorMapping']['propertyArrayAccessCheck'] && isset($object[$accessor])) {
                // ???
                $object = $object[$accessor];
            }else {
                return null;
            }
        }
        return $object;
    }
}
