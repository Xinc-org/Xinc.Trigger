<?php
/**
 * Xinc - Continuous Integration.
 *
 * PHP version 5
 *
 * @category   Development
 * @package    Xinc.Trigger
 * @subpackage Trigger
 * @author     Alexander Opitz <opitz.alexander@gmail.com>
 * @copyright  2013 Alexander Opitz, Leipzig
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU/LGPL, see license.php
 *             This file is part of Xinc.
 *             Xinc is free software; you can redistribute it and/or modify
 *             it under the terms of the GNU Lesser General Public License as
 *             published by the Free Software Foundation; either version 2.1 of
 *             the License, or (at your option) any later version.
 *
 *             Xinc is distributed in the hope that it will be useful,
 *             but WITHOUT ANY WARRANTY; without even the implied warranty of
 *             MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *             GNU Lesser General Public License for more details.
 *
 *             You should have received a copy of the GNU Lesser General Public
 *             License along with Xinc, write to the Free Software Foundation,
 *             Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * @link       https://github.com/Xinc-org/Xinc.Trigger
 */

namespace Xinc\Trigger\Task;

abstract class TaskAbstract extends \Xinc\Core\Task\TaskAbstract //implements Xinc_Build_Scheduler_Interface
{
    /**
     * @var integer Task Slot INIT_PROCESS
     */
    protected $pluginSlot = \Xinc\Core\Task\Slot::INIT_PROCESS;

    /**
     * @var integer next calculated job runtime
     */
    protected $nextJobRunTime = null;

    /**
     * Initialize the task
     *
     * @param Xinc\Core\Job\JobInterface $job Build to initialize this task for.
     *
     * @return void
     */
    public function init(\Xinc\Core\Job\JobInterface $job = null)
    {
        // $job->addScheduler($this);
    }

    /**
     * Process the task
     *
     * @param Xinc\Core\Job\JobInterface $job Build to process this task for.
     *
     * @return void
     */
    public function process(\Xinc\Core\Job\JobInterface $job)
    {
        if (time() >= $this->nextJobRunTime) {
            $this->nextJobRunTime = null;
        }
    }

    /**
     * Gets the next calculated job runtime for given project.
     *
     * @param Xinc\Core\Models\Project $project
     *
     * @return integer next job runtime as timestamp
     */
    public function getNextProjectRunTime(\Xinc\Core\Models\Project $project)
    {
        if ($this->nextJobRunTime === null) {
            if ($project->getStatus() !== \Xinc\Core\Project\Status::ENABLED) {
                return null;
            }

            $this->nextJobRunTime = $this->getNextTime($project->getLastJob());
        }

        return $this->nextJobRunTime;
    }

    /**
     * Calculates the real next job runtime dependend on lastJob.
     *
     * @param Xinc\Core\Job\JobInterface $lastJob
     *
     * @return integer next job runtime as timestamp
     */
    abstract public function getNextTime(\Xinc\Core\Job\JobInterface $lastJob = null);
}
