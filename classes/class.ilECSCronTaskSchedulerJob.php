<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Open Text Cron job definition
 *
 * @author Stefan Meyer <smeyer.ilias@gmx.de>
 *
 */
class ilECSCronTaskSchedulerJob extends \ilCronJob
{
    /**
     * @var int
     */
    const DEFAULT_SCHEDULE_VALUE  = 1;

    /**
     * @var null | \ilLogger
     */
    private $logger = null;

    /**
     * @var null | \ilECSCronTaskSchedulerPlugin
     */
    private $plugin = null;

    /**
     * @var null | \ilCronJobResult
     */
    private $result = null;


    /**
     * ilECSCronTaskSchedulerJob constructor.
     */
    public function __construct()
    {
        global $DIC;

        $this->logger = $DIC->logger()->wsrv();
        $this->plugin = \ilECSCronTaskSchedulerPlugin::getInstance();
        $this->result = new \ilCronJobResult();
    }

    /**
     * @return string|void
     */
    public function getId()
    {
        return $this->plugin->getId();
    }

    /**
     * Is to be activated on "installation"
     * @return boolean
     */
    public function hasAutoActivation()
    {
        return false;
    }

    /**
     * Can the schedule be configured?
     * @return boolean
     */
    public function hasFlexibleSchedule()
    {
        return true;
    }

    /**
     * Get schedule type
     * @return int
     */
    public function getDefaultScheduleType()
    {
        return self::SCHEDULE_TYPE_IN_HOURS;
    }

    /**
     * Get schedule value
     * @return int|array
     */
    public function getDefaultScheduleValue()
    {
        return self::DEFAULT_SCHEDULE_VALUE;
    }

    /**
     * @return string|void
     */
    public function getTitle()
    {
        return $this->plugin->txt('cron_title');
    }

    public function getDescription()
    {
        return $this->plugin->txt('cron_title_description');
    }

    /**
     * Run job
     * @return ilCronJobResult
     */
    public function run()
    {
        $this->logger->info('Starting ecs cron task scheduler...');

        $servers = \ilECSServerSettings::getInstance();

        foreach ($servers->getServers() as $server) {
            try {
                $this->logger->info('Starting task execution for ecs server: ' . $server->getTitle());
                $scheduler = \ilECSTaskScheduler::_getInstanceByServerId($server->getServerId());
                $scheduler->startTaskExecution();
            } catch (\Exception $e) {
                $this->result->setStatus(\ilCronJobResult::STATUS_CRASHED);
                $this->result->setMessage($e->getMessage());
                $this->logger->error('ECS task execution failed with message: ' . $e->getMessage());
                return $this->result;
            }
        }
        $this->result->setStatus(\ilCronJobResult::STATUS_OK);
        return $this->result;
    }
}