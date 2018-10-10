<?php

namespace Oro\Component\MessageQueue\Job;

/**
 * Abstract MQ job extension
 */
abstract class AbstractExtension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function onPreRunUnique(Job $job)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onPostRunUnique(Job $job, $jobResult)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onPreCreateDelayed(Job $job)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onPostCreateDelayed(Job $job, $createResult)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onPreRunDelayed(Job $job)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onPostRunDelayed(Job $job, $jobResult)
    {
    }

    /**
     * Executed if root job was interrupted.
     *
     * @param Job $job
     */
    public function onCancel(Job $job)
    {
    }

    /**
     * Executed if job was crashed during callback processing.
     *
     * @param Job $job
     */
    public function onError(Job $job)
    {
    }
}
