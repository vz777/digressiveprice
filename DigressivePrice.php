<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace DigressivePrice;

use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Thelia\Module\BaseModule;
use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Install\Database;

class DigressivePrice extends BaseModule
{
    const DOMAIN = 'digressiveprice';

    public function postActivation(ConnectionInterface $con = null): void
    {
        parent::postActivation($con);

        if (!is_null($con)) {
            $database = new Database($con);
            $database->insertSql(null, array(__DIR__ . '/Config/create.sql'));
        }
    }

    /**
     * This method is called when a newer version of the plugin is installed
     *
     * @param string $currentVersion the current (installed) module version, as defined in the module.xml file
     * @param string $newVersion the new module version, as defined in the module.xml file
     * @param ConnectionInterface $con
     */
    public function update($currentVersion, $newVersion, ConnectionInterface $con = null): void
    {
        // Change foreign key configuration
        if (! is_null($con) && $currentVersion == '2.0') {
            $database = new Database($con);
            $database->insertSql(null, array(__DIR__ . '/Config/update-2.0.sql'));
        }
    }

    public function destroy(ConnectionInterface $con = null, $deleteModuleData = false): void
    {
        parent::destroy($con, $deleteModuleData);

        if (!is_null($con) && $deleteModuleData === true) {
            $database = new Database($con);
            $database->insertSql(null, array(__DIR__ . '/Config/delete.sql'));
        }
    }

    public static function configureServices(ServicesConfigurator $servicesConfigurator): void
    {
        $servicesConfigurator->load(self::getModuleCode().'\\', __DIR__)
            ->exclude([THELIA_MODULE_DIR . ucfirst(self::getModuleCode()). "/I18n/*"])
            ->autowire(true)
            ->autoconfigure(true);
    }
}
