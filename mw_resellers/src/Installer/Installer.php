<?php

namespace Weble\Module\Resellers\Installer;

class Installer
{
    private $module;
    private array $hooks = [];

    public function __construct($module)
    {
        $this->module = $module;
    }

    public function install(): bool
    {
        return $this->installSql() && $this->registerHooks();
    }

    public function uninstall(): bool
    {
        return $this->uninstallSql();
    }

    private function registerHooks(): bool
    {
        foreach ($this->hooks as $hook) {
            if (!$this->module->registerHook($hook)) {
                return false;
            }
        }

        return true;
    }

    private function unregisterHooks()
    {
        foreach ($this->hooks as $hook) {
            if (!$this->module->unregisterHook($hook)) {
                return false;
            }
        }

        return true;
    }

    private function installSql(): bool
    {
        $queries = [];

        $queries[] =    "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "reseller_groups` (
    `id_reseller_group` int(11) NOT NULL AUTO_INCREMENT,
    `name` text NOT NULL,
    `zone` text,
    `active` tinyint(1),
    PRIMARY KEY  (`id_reseller_group`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;";

        $queries[] =    "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "resellers` (
    `id_reseller` int(11) NOT NULL AUTO_INCREMENT,
    `id_reseller_group` int(11),
    `name` text NOT NULL,
    `address` text,
    `city` text,
    `phone` varchar(255),
    `email` varchar(255),
    `lat` varchar(255),
    `lng` varchar(255),
    `active` tinyint(1),
    PRIMARY KEY  (`id_reseller`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;";

        foreach ($queries as $query) {
            if (!\Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }

    private function uninstallSql(): bool
    {
        $queries = [];

        $queries[] = "DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'mw_reseller_groups`";
        $queries[] = "DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'mw_resellers`";

        foreach ($queries as $query) {
            if (!\Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }

    public function installTab(): \Tab
    {
        // Get Tab if exists else create a new instance
        $tab = \TabCore::getInstanceFromClassName($this->module->listRouteLegacyController);

        if (!$tab->id) {
            $tab->name = [];
            foreach (\LanguageCore::getLanguages() as $lang) {
                $tab->name[$lang['id_lang']] = $this->module->displayName;
            }

            $tab->route_name = $this->module->listRouter;
            $tab->class_name = $this->module->listRouteLegacyController;
            $tab->enabled = 1;
            $tab->active = 1;
            $tab->icon = "people_outline";
            $tab->id_parent = (int) \TabCore::getIdFromClassName('IMPROVE');;
        }

        return $tab->save();
    }

    public function uninstallTab(): bool
    {
        $tab = \TabCore::getInstanceFromClassName($this->module->listRouteLegacyController);

        if (!$tab->id) {
            return true;
        }

        return $tab->delete();
    }
}
