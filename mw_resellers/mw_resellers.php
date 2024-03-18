<?php

use Doctrine\ORM\EntityManager;
use Weble\Module\Resellers\Entity\ResellerGroup;
use Weble\Module\Resellers\Installer\Installer;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once "vendor/autoload.php";

class Mw_resellers extends Module
{
    protected $config_form = false;
    public string $listRouteLegacyController;
    public string $listRouter;

    public function __construct()
    {
        $this->name = 'mw_resellers';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Weble';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->config_form = true;

        parent::__construct();

        $this->displayName = $this->l('Rivenditori');
        $this->description = $this->l('Mostra una pagina per ogni gruppo di rivenditori, con lista e mappa integrata');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];

        $this->listRouteLegacyController = "MwResellersList";
        $this->listRouter = 'mw_resellers_list';

    }

    public function install()
    {
        $installer = new Installer($this);

        return parent::install() && $installer->install() && $installer->installTab();
    }

    public function uninstall()
    {
        $installer = new Installer($this);

        return parent::uninstall() && $installer->uninstall() && $installer->uninstallTab();
    }

    public function getContent()
    {
        if ((Tools::isSubmit('submitMw_resellersModule')) === true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        return $this->renderForm();
    }

    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitMw_resellersModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    protected function getConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'col' => 6,
                        'type' => 'text',
                        'name' => strtoupper($this->name) . '_IT',
                        'label' => $this->l('Nome Gruppo Italia'),
                        'lang' => true,
                    ],
                    [
                        'col' => 6,
                        'type' => 'text',
                        'name' => strtoupper($this->name) . '_IT_CENTER_COORDS_LAT',
                        'desc' => 'Latitudine del centro mappa al caricamento della pagina',
                        'label' => $this->l('Latitudine Centro Mappa Italia')
                    ],
                    [
                        'col' => 6,
                        'type' => 'text',
                        'name' => strtoupper($this->name) . '_IT_CENTER_COORDS_LNG',
                        'desc' => 'Longitudine del centro mappa al caricamento della pagina',
                        'label' => $this->l('Longitudine Centro Mappa Italia')
                    ],
                    [
                        'col' => 6,
                        'type' => 'text',
                        'name' => strtoupper($this->name) . '_SP',
                        'label' => $this->l('Nome Gruppo Spagna'),
                        'lang' => true,
                    ],
                    [
                        'col' => 6,
                        'type' => 'text',
                        'name' => strtoupper($this->name) . '_SP_CENTER_COORDS_LAT',
                        'desc' => 'Latitudine del centro mappa al caricamento della pagina',
                        'label' => $this->l('Latitudine Centro Mappa Spagna')
                    ],
                    [
                        'col' => 6,
                        'type' => 'text',
                        'name' => strtoupper($this->name) . '_SP_CENTER_COORDS_LNG',
                        'desc' => 'Longitudine del centro mappa al caricamento della pagina',
                        'label' => $this->l('Longitudine Centro Mappa Spagna')
                    ],
                    [
                        'col' => 6,
                        'type' => 'text',
                        'name' => strtoupper($this->name) . '_WW',
                        'label' => $this->l('Nome Gruppo Mondo'),
                        'lang' => true,
                    ],
                    [
                        'col' => 6,
                        'type' => 'text',
                        'name' => strtoupper($this->name) . '_WW_CENTER_COORDS_LAT',
                        'desc' => 'Latitudine del centro mappa al caricamento della pagina',
                        'label' => $this->l('Latitudine Centro Mappa Mondo')
                    ],
                    [
                        'col' => 6,
                        'type' => 'text',
                        'name' => strtoupper($this->name) . '_WW_CENTER_COORDS_LNG',
                        'desc' => 'Longitudine del centro mappa al caricamento della pagina',
                        'label' => $this->l('Longitudine Centro Mappa Mondo')
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');

        $resellerGroupRepository = $em->getRepository(ResellerGroup::class);

        $itGroup = $resellerGroupRepository->findOneBy(['zone' => 'it']);
        $spGroup = $resellerGroupRepository->findOneBy(['zone' => 'sp']);
        $wwGroup = $resellerGroupRepository->findOneBy(['zone' => 'ww']);

        $values = [];
        foreach (LanguageCore::getLanguages() as $language) {
            $values[strtoupper($this->name) . "_IT"][$language['id_lang']] = $itGroup->getName($language['iso_code']);
            $values[strtoupper($this->name) . "_SP"][$language['id_lang']] = $spGroup->getName($language['iso_code']);
            $values[strtoupper($this->name) . "_WW"][$language['id_lang']] = $wwGroup->getName($language['iso_code']);
        }

        $values[strtoupper($this->name) . "_IT_CENTER_COORDS_LAT"] = $itGroup->getMapCenterByDirection('lat');
        $values[strtoupper($this->name) . "_IT_CENTER_COORDS_LNG"] = $itGroup->getMapCenterByDirection('lng');

        $values[strtoupper($this->name) . "_SP_CENTER_COORDS_LAT"] = $spGroup->getMapCenterByDirection('lat');
        $values[strtoupper($this->name) . "_SP_CENTER_COORDS_LNG"] = $spGroup->getMapCenterByDirection('lng');

        $values[strtoupper($this->name) . "_WW_CENTER_COORDS_LAT"] = $wwGroup->getMapCenterByDirection('lat');
        $values[strtoupper($this->name) . "_WW_CENTER_COORDS_LNG"] = $wwGroup->getMapCenterByDirection('lng');

        return $values;
    }


    /**
     * Save form data.
     */
    protected function postProcess()
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');

        $resellerGroupRepository = $em->getRepository(ResellerGroup::class);

        $itGroup = $resellerGroupRepository->findOneBy(['zone' => 'it']);
        $spGroup = $resellerGroupRepository->findOneBy(['zone' => 'sp']);
        $wwGroup = $resellerGroupRepository->findOneBy(['zone' => 'ww']);

        foreach (Language::getLanguages() as $language) {
            $itGroup->setName(Tools::getValue(strtoupper($this->name) . "_IT_{$language['id_lang']}"), $language['iso_code']);
            $spGroup->setName(Tools::getValue(strtoupper($this->name) . "_SP_{$language['id_lang']}"), $language['iso_code']);
            $wwGroup->setName(Tools::getValue(strtoupper($this->name) . "_WW_{$language['id_lang']}"), $language['iso_code']);
        }

        $itGroup->setMapCenter(Tools::getValue(strtoupper($this->name) . "_IT_CENTER_COORDS_LAT", null), 'lat');
        $itGroup->setMapCenter(Tools::getValue(strtoupper($this->name) . "_IT_CENTER_COORDS_LNG", null), 'lng');

        $spGroup->setMapCenter(Tools::getValue(strtoupper($this->name) . "_SP_CENTER_COORDS_LAT", null), 'lat');
        $spGroup->setMapCenter(Tools::getValue(strtoupper($this->name) . "_SP_CENTER_COORDS_LNG", null), 'lng');

        $wwGroup->setMapCenter(Tools::getValue(strtoupper($this->name) . "_WW_CENTER_COORDS_LAT", null), 'lat');
        $wwGroup->setMapCenter(Tools::getValue(strtoupper($this->name) . "_WW_CENTER_COORDS_LNG", null), 'lng');

        $em->flush();
    }
}
