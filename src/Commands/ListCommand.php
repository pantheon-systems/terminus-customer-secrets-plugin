<?php

namespace Pantheon\TerminusCustomerSecrets\Commands;

use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Exceptions\TerminusNotFoundException;
use Pantheon\Terminus\Exceptions\TerminusException;
use Pantheon\TerminusCustomerSecrets\SecretsApi\SecretsApiAwareTrait;
use Pantheon\Terminus\Commands\StructuredListTrait;
use Pantheon\Terminus\Site\SiteAwareTrait;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Consolidation\OutputFormatters\StructuredData\RowsOfFields;

/**
 * Class ListCommand
 * List secrets for a given site.
 *
 * @package Pantheon\Terminus\Commands\CustomerSecrets
 */
class ListCommand extends CustomerSecretsBaseCommand implements SiteAwareInterface
{
    use StructuredListTrait;
    use SiteAwareTrait;

    /**
     * Lists secrets for a specific site.
     *
     * @authorize
     * @filter-output
     *
     * @command customer-secrets:list
     * @aliases customer-secrets
     *
     * @field-labels
     *   name: Secret name
     *   value: Secret value
     *
     * @option boolean $debug Run command in debug mode
     * @param string $site_id The name or UUID of a site to retrieve information on
     * @param array $options
     * @return RowsOfFields
     *
     * @usage <site> Lists all secrets for current site.
     * @usage <site> --debug List all secrets for current site (debug mode).
     *
     * @throws \Pantheon\Terminus\Exceptions\TerminusException
     */
    public function listSecrets($site_id, array $options = ['debug' => false,])
    {
        if ($this->getSite($site_id)) {
            $secrets = $this->secretsApi->listSecrets($site_id, $options['debug']);
            $print_options = [
                'message' => 'You have no Customer Secrets.'
            ];

            return $this->getTableFromData($secrets, $print_options);
        }
    }

    /**
     * @param array $data Data already serialized (i.e. not a TerminusCollection)
     * @param array $options Elements as follow
     *        string $message Message to emit if the collection is empty.
     *        array $message_options Values to interpolate into the error message.
     *        function $sort A function to sort the data using
     * @return RowsOfFields Returns a RowsOfFields-type object with applied filters
     */
    protected function getTableFromData(
        array $data,
        array $options = [],
        $date_attributes = []
    ) {
        if (count($data) === 0) {
            $message = $options['message'];
            $options = isset($options['message_options']) ? $options['message_options'] : [];
            $this->log()->warning($message, $options);
        }
        $table = new RowsOfFields($data);
        return $table;
    }
}
