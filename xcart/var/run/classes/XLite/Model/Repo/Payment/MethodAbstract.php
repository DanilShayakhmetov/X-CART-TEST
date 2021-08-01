<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Payment;

use Includes\Utils\Module\Manager;
use Includes\Utils\Module\Module;
use XLite\Model\QueryBuilder\AQueryBuilder;

/**
 * Payment method repository
 *
 *  Api\Operation\Read(modelClass="XLite\Model\Payment\Method", summary="Retrieve payment method by id")
 *  Api\Operation\ReadAll(modelClass="XLite\Model\Payment\Method", summary="Retrieve payment methods by conditions")
 *  Api\Operation\Update(modelClass="XLite\Model\Payment\Method", summary="Update payment method by id")
 */
abstract class MethodAbstract extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Names of fields that are used in search
     */
    const P_ENABLED             = 'enabled';
    const P_MODULE_ENABLED      = 'moduleEnabled';
    const P_ADDED               = 'added';
    const P_ONLY_PURE_OFFLINE   = 'onlyPureOffline';
    const P_ONLY_MODULE_OFFLINE = 'onlyModuleOffline';
    const P_POSITION            = 'position';
    const P_TYPE                = 'type';

     // Use the Force, Luke
    const P_ORDER_BY_FORCE       = 'orderByForce';

    const P_NAME                = 'name';
    const P_COUNTRY             = 'country';
    const P_EX_COUNTRY          = 'exCountry';

    /**
     * Name of the field which is used for default sorting (ordering)
     */
    const FIELD_DEFAULT_POSITION = 'orderby';

    /**
     * Repository type
     *
     * @var string
     */
    protected $type = self::TYPE_SECONDARY;

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'orderby';

    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = array(
        array('service_name'),
    );

    /**
     * Add the specific joints with the translation table
     *
     * @param AQueryBuilder $queryBuilder
     * @param string                     $alias
     * @param string                     $translationsAlias
     * @param string                     $code
     *
     * @return AQueryBuilder
     */
    protected function addTranslationJoins($queryBuilder, $alias, $translationsAlias, $code)
    {
        $queryBuilder
            ->linkLeft(
                $alias . '.translations',
                $translationsAlias,
                \Doctrine\ORM\Query\Expr\Join::WITH,
                $translationsAlias . '.code = :lng OR ' . $translationsAlias . '.code = :lng2'
            )
            ->setParameter('lng', $code)
            ->setParameter('lng2', 'en');

        return $queryBuilder;
    }

    /**
     * Update entity
     *
     * @param \XLite\Model\AEntity $entity Entity to update
     * @param array                $data   New values for entity properties
     * @param boolean              $flush  Flag OPTIONAL
     *
     * @return void
     */
    public function update(\XLite\Model\AEntity $entity, array $data = array(), $flush = self::FLUSH_BY_DEFAULT)
    {
        $name = null;
        foreach ($entity->getTranslations() as $translation) {
            if ($translation->getName()) {
                $name = $translation->getName();
                break;
            }
        }

        if ($name) {
            foreach ($entity->getTranslations() as $translation) {
                if (!$translation->getName()) {
                    $translation->setName($name);
                }
            }
        }

        parent::update($entity, $data, $flush);
    }

    /**
     * Prepare certain search condition for module name
     * @Api\Condition(description="Retrieve payment method by its name", type="string")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndName(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ('' !== $value) {
            $queryBuilder
                ->andWhere('translations.name LIKE :name')
                ->setParameter('name', "%" . $value . "%");
        }
    }

    /**
     * Prepare certain search condition for module name
     * @Api\Condition(description="Retrieve payment method by country", type="string")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndCountry(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $alias = $this->getMainAlias($queryBuilder);

        $country = $value ?: \XLite\Core\Config::getInstance()->Company->location_country;
        $queryBuilder->linkLeft($alias . '.countryPositions', 'countryPosition', 'WITH', 'countryPosition.countryCode = :countryCode')
            ->setParameter('countryCode', $country);
        $queryBuilder->addSelect('(CASE WHEN countryPosition.adminPosition IS NULL THEN 1 ELSE 0 END) AS HIDDEN adminPosition');

        if (!empty($value)) {

            $queryBuilder->andWhere(
                    $queryBuilder->expr()->orX(
                        $alias . '.countries LIKE :country',
                        $alias . '.countries = :emptyArray',
                        $alias . '.countries = :undefinedValue',
                        $alias . '.countries = :emptyValue'
                    )
                )
                ->setParameter('country', '%"' . $value . '"%')
                ->setParameter('emptyArray', 'a:0:{}')
                ->setParameter('undefinedValue', 'N;')
                ->setParameter('emptyValue', '');
        }
    }

    /**
     * Prepare certain search condition for module name
     * @Api\Condition(description="Retrieve payment method by excluded country name", type="string")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndExCountry(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (!empty($value)) {
            $alias = $this->getMainAlias($queryBuilder);

            $queryBuilder->andWhere(
                    $queryBuilder->expr()->not(
                        $alias . '.exCountries LIKE :country'
                    )
                )
                ->setParameter('country', '%"' . $value . '"%');
        }
    }

    /**
     * Prepare certain search condition for enabled flag
     * @Api\Condition(description="Retrieve payment method by its enabled state", type="boolean")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $queryBuilder
            ->andWhere($this->getMainAlias($queryBuilder) . '.enabled = :enabled_value')
            ->setParameter('enabled_value', $value);
    }

    /**
     * Prepare certain search condition for moduleEnabled flag
     * @Api\Condition(description="Retrieve payment method by module enabled state", type="boolean")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndModuleEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $enabledModules = array_map(
            function ($module) {
                /** @var Module $module */
                return $module->author . '_' . $module->name;
            },
            array_merge(Manager::getRegistry()->getEnabledPaymentModules(),Manager::getRegistry()->getEnabledShippingModules())
        );

        $enabledModules[] = '';

        $queryBuilder
            ->andWhere($queryBuilder->expr()->in($this->getMainAlias($queryBuilder) . '.moduleName', $enabledModules));
    }

    /**
     * Prepare certain search condition for added flag
     * @Api\Condition(description="Retrieve payment method by its added\not added state", type="boolean")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndAdded(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (isset($value)) {
            $queryBuilder
                ->andWhere($this->getMainAlias($queryBuilder) . '.added = :added_value')
                ->setParameter('added_value', $value);
        }
    }

    /**
     * Prepare certain search condition for onlyModuleOffline flag
     * @Api\Condition(description="Get only offline methods", type="boolean")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndOnlyPureOffline(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $alias = $this->getMainAlias($queryBuilder);
            $queryBuilder
                ->andWhere($alias . '.class = :class AND ' . $alias . '.type = :offlineType')
                ->setParameter('class', 'Model\Payment\Processor\Offline')
                ->setParameter('offlineType', \XLite\Model\Payment\Method::TYPE_OFFLINE);
        }
    }

    /**
     * Prepare certain search condition for onlyModuleOffline flag
     * @Api\Condition(description="Get only offline methods (added by module)", type="boolean")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndOnlyModuleOffline(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $alias = $this->getMainAlias($queryBuilder);
            $queryBuilder
                ->andWhere($alias . '.class != :class AND ' . $alias . '.type = :offlineType')
                ->setParameter('class', 'Model\Payment\Processor\Offline')
                ->setParameter('offlineType', \XLite\Model\Payment\Method::TYPE_OFFLINE);
        }
    }

    /**
     * Prepare certain search condition for position
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndPosition(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value, $countOnly)
    {
        if (!$countOnly) {
            list($sort, $order) = $value;

            $queryBuilder->addOrderBy($this->getMainAlias($queryBuilder) . '.' . $sort, $order);
        }
    }

    /**
     * @Api\Condition(description="Filter methods by type", type="string")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndType(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $alias = $this->getMainAlias($queryBuilder);
            if (is_array($value)) {
                $queryBuilder->addInCondition($alias . '.type', $value);

            } else {
                $queryBuilder->andWhere($alias . '.type = :type')
                    ->setParameter('type', $value);
            }
        }
    }

    /**
     * Prepare certain search condition for position
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndOrderByForce(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value, $countOnly)
    {
        if (!$countOnly) {
            list($sort, $order) = $this->getSortOrderValue($value);

            $queryBuilder->orderBy($sort, $order);
            $this->assignDefaultOrderBy($queryBuilder);
        }
    }

    // }}}

    // {{{ Finders

    /**
     * Find all methods
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function findAllMethods()
    {
        return $this->defineAllMethodsQuery()->getResult();
    }

    /**
     * Find all active and ready for checkout payment methods.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function findAllActive()
    {
        return $this->defineAllActiveQuery()->getResult();
    }

    /**
     * Check - has active payment modules or not
     *
     * @return bool
     */
    public function hasActivePaymentModules()
    {
        return (bool) \count(Manager::getRegistry()->getEnabledPaymentModules());
        // return 0 < $this->defineHasActivePaymentModulesQuery()->getSingleScalarResult();
    }

    /**
     * Find offline method (not from modules)
     *
     * @return array
     */
    public function findOffline()
    {
        $list = array();

        foreach ($this->defineFindOfflineQuery()->getResult() as $method) {
            if (!preg_match('/\\\Module\\\/Ss', $method->getClass())) {
                $list[] = $method;
            }
        }

        return $list;
    }

    /**
     * Find offline method (only from modules)
     *
     * @return array
     */
    public function findOfflineModules()
    {
        $list = array();

        foreach ($this->defineFindOfflineQuery()->getResult() as $method) {
            if (preg_match('/\\\Module\\\/Ss', $method->getClass())) {
                $list[] = $method;
            }
        }

        return $list;
    }

    /**
     * Find payment methods by specified type for dialog 'Add payment method'
     *
     * @param string $type Payment method type
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function findForAdditionByType($type)
    {
        return $this->defineAdditionByTypeQuery($type)->getResult();
    }

    /**
     * Define query for findAdditionByType()
     *
     * @param string $type Payment method type
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineAdditionByTypeQuery($type)
    {
        $qb = $this->createPureQueryBuilder('m');

        $this->prepareCndType($qb, $type, false);
        $this->prepareCndOrderBy($qb, array('m.adminOrderby'), false);

        return $this->addOrderByForAdditionByTypeQuery($qb);
    }

    /**
     * Add ORDER BY for findAdditionByType() query
     *
     * @param \Doctrine\ORM\QueryBuilder $qb Query builder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function addOrderByForAdditionByTypeQuery($qb)
    {
        return $qb->addOrderBy('m.moduleName', 'asc');
    }

    /**
     * Define query for findAllMethods() method
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineAllMethodsQuery()
    {
        return $this->createQueryBuilder();
    }

    /**
     * Define query for findAllActive() method
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineAllActiveQuery()
    {
        return $this->createQueryBuilder()
            ->andWhere('m.enabled = :true')
            ->andWhere('m.added = :true')
            ->setParameter('true', true);
    }

    /**
     * Define query for hasActivePaymentModules() method
     * @todo: remove, unused
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineHasActivePaymentModulesQuery()
    {
        //return $this->createPureQueryBuilder()
        //    ->select('COUNT(m.method_id) cns')
        //    ->andWhere('m.type != :offline')
        //    ->andWhere('m.moduleEnabled = :moduleEnabled')
        //    ->setParameter('offline', \XLite\Model\Payment\Method::TYPE_OFFLINE)
        //    ->setParameter('moduleEnabled', true)
        //    ->setMaxResults(1);
    }

    /**
     * Define query for findOffline() method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindOfflineQuery()
    {
        return $this->createPureQueryBuilder()
            ->setParameter('offline', \XLite\Model\Payment\Method::TYPE_OFFLINE);
    }

    // }}}

    // {{{ Update payment methods from marketplace

    /**
     * Update payment methods with data received from the marketplace
     *
     * @param array  $data List of payment methods received from marketplace
     * @param string $countryCode
     *
     * @return void
     */
    public function updatePaymentMethods($data, $countryCode = '')
    {
        if (!empty($data) && is_array($data)) {
            $methods = [];
            $methodsFromMarketplace = [];

            // Get all payment methods list as an array
            $tmpMethods = $this->createQueryBuilder('m')
                ->select('m')
                ->getQuery()
                ->getArrayResult();

            if ($tmpMethods) {
                // Prepare associative array of existing methods with 'service_name' as a key
                foreach ($tmpMethods as $m) {
                    $methods[$m['service_name']] = $m;
                }
            }

            foreach ($data as $i => $extMethod) {

                if (!empty($extMethod['service_name'])) {

                    unset($extMethod['enabled'], $extMethod['added']);
                    $data[$i] = $extMethod;
                    $methodsFromMarketplace[] = $extMethod['service_name'];

                    if (isset($methods[$extMethod['service_name']])) {

                        // Method already exists in the database

                        if (!$methods[$extMethod['service_name']]['fromMarketplace']) {
                            $data[$i] = [
                                'service_name' => $extMethod['service_name'],
                                'countries'    => !empty($extMethod['countries']) ? $extMethod['countries'] : [],
                                'exCountries'  => !empty($extMethod['exCountries']) ? $extMethod['exCountries'] : [],
                                'orderby'      => !empty($extMethod['orderby']) ? $extMethod['orderby'] : 0,
                            ];
                        }

                    } else {
                        $data[$i]['fromMarketplace'] = 1;
                    }

                    if (isset($data[$i]['orderby'])) {
                        $data[$i]['adminOrderby'] = $data[$i]['orderby'];

                        $data[$i]['countryPositions'] = [
                            [
                                'countryCode' => $countryCode,
                                'adminPosition' => $data[$i]['orderby'],
                            ]
                        ];

                        unset($data[$i]['orderby']);
                    }

                } else {
                    // Wrong data row, ignore this
                    unset($data[$i]);
                }
            }

            $this->removeIrrelevantPaymentMethods($methodsFromMarketplace);

            // Save data as temporary yaml file
            $yaml = \Symfony\Component\Yaml\Yaml::dump(['XLite\\Model\\Payment\\Method' => $data]);

            $yamlFile = LC_DIR_TMP . 'pm.yaml';

            \Includes\Utils\FileManager::write(LC_DIR_TMP . 'pm.yaml', $yaml);

            // Update database from yaml file
            \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);
        }
    }

    /**
     * Remove irrelevant payment methods
     *
     * @param array $methodsFromMarketplace
     *
     * @return void
     */
    protected function removeIrrelevantPaymentMethods($methodsFromMarketplace)
    {
        $notInstalledPaymentsMethods = $this->getQueryBuilder()
            ->select('m.service_name')
            ->from($this->_entityName, 'm')
            ->where('m.fromMarketplace = 1')
            ->getArrayResult();

        $irrelevantPaymentMethods = array_filter($notInstalledPaymentsMethods, function($method) use($methodsFromMarketplace) {
            return !in_array($method['service_name'], $methodsFromMarketplace);
        });

        foreach ($irrelevantPaymentMethods as $methodToRemove) {
            $this->getQueryBuilder()
                ->delete($this->_entityName, 'm')
                ->where('m.fromMarketplace = 1')
                ->andWhere('m.service_name = :serviceName')
                ->setParameter('serviceName', $methodToRemove['service_name'])
                ->execute();
        }
    }

    // }}}
}
