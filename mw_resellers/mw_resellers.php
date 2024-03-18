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

        dump($this->context->link->getModuleLink($this->name, 'italy'));

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
                        'name' => strtoupper($this->name) . '_SP',
                        'label' => $this->l('Nome Gruppo Spagna'),
                        'lang' => true,
                    ],
                    [
                        'col' => 6,
                        'type' => 'text',
                        'name' => strtoupper($this->name) . '_WW',
                        'label' => $this->l('Nome Gruppo Mondo'),
                        'lang' => true,
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

        foreach ($this->getConfigForm()['form']['input'] as $field) {
            if ($field['lang']) {
                $values[$field['name']] = Configuration::getConfigInMultipleLangs($field['name']);
            } else {
                $values[$field['name']] = Configuration::get($field['name']);
            }
        }

        $values = [];
        foreach (LanguageCore::getLanguages() as $language) {
            $values[strtoupper($this->name) . "_IT"][$language['id_lang']] = $itGroup->getName($language['iso_code']);
            $values[strtoupper($this->name) . "_SP"][$language['id_lang']] = $spGroup->getName($language['iso_code']);
            $values[strtoupper($this->name) . "_WW"][$language['id_lang']] = $wwGroup->getName($language['iso_code']);
        }

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

        $em->flush();
    }
}
