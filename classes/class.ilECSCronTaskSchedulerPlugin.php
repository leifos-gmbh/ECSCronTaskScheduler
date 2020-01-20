<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Open Text Cron job definition
 * 
 * @author Stefan Meyer <smeyer.ilias@gmx.de>
 *
 */
class ilECSCronTaskSchedulerPlugin extends ilCronHookPlugin
{
    /**
     * @var string
     */
    const CTYPE = 'Services';

    /**
     * @var string
     */
    const CNAME = 'Cron';

    /**
     * @var string
     */
    const SLOT_ID = 'crnhk';

    /**
     * @var string
     */
    const PNAME = 'ECSCronTaskScheduler';


    /**
     * @var null | \ilECSCronTaskSchedulerPlugin
     */
    private static $instance =  null;


    /**get plugin instance
     *
     * @return \ilECSCronTaskSchedulerPlugin
     */
    public static function getInstance()
    {
        if(!self::$instance instanceof \ilECSCronTaskSchedulerPlugin) {
            self::$instance = \ilPluginAdmin::getPluginObject(
                self::CTYPE,
                self::CNAME,
                self::SLOT_ID,
                self::PNAME
            );
        }
        return self::$instance;
    }

    /**
     * init plugin
     */
    protected function init()
    {
        $this->initAutoload();
    }

    /**
     *
     */
    protected function initAutoload()
    {
        spl_autoload_register(
            [
                $this, 'autoLoad'
            ]
        );
    }

    /**
     * Auto load implementation
     *
     * @param string class name
     */
    private final function autoLoad($a_classname)
    {
        $class_file = $this->getClassesDirectory().'/class.'.$a_classname.'.php';
        if(@include_once($class_file))
        {
            return;
        }
    }


    /**
     * @inheritdoc
     * @return \ilECSCronTaskSchedulerJob[]
     */
    public function getCronJobInstances()
    {
        $job = new \ilECSCronTaskSchedulerJob();
        return [
            $job
        ];
    }

    /**
     * @param $a_job_id
     * @return \ilECSCronTaskSchedulerJob
     */
    public function getCronJobInstance($a_job_id)
    {
        $job = new \ilECSCronTaskSchedulerJob();
        return $job;
    }

    /**
     * @return string|void
     */
    public function getPluginName()
    {
        return self::PNAME;
    }
}