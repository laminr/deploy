<?php
/**
 * Created by PhpStorm.
 * User: thibault
 * Date: 18/03/15
 * Time: 08:44
 */

namespace Rsv\DeployBundle\Business;

use Rsv\DeployBundle\Entity\Project;
use Psr\Log\LoggerInterface;

class SshBusiness {

    private $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    private function getConnection($domain, $user, $passwd) {
        $this->logger->info('server connection');

        $logged = null;
        //echo "domain: $domain user: $user password: $passwd";

        try {
            $connection = ssh2_connect($domain, 22);
            $logged = ssh2_auth_password($connection, $user, $passwd);
        } catch (Exception $e) {
            log_message('error', 'erreur connection SSH :'.$domain);
        }

        $this->logger->info('logged:'.($connection != null));
        return $logged ? $connection : null;
    }


    public function execute(Project $where, $command) {

        $values = array();

        $domain	= $where->getDomain();
        $user 	= $where->getUser();
        $passwd = $where->getPassword();

        $connection = $this->getConnection($domain, $user, $passwd);

        if ($connection) {

            $this->logger->info('SSH execute:'.$command);

            $cmd = ssh2_exec($connection, $command);
            //sleep(1);
            stream_set_blocking($cmd, true);

            $errors = ssh2_fetch_stream($cmd, SSH2_STREAM_STDERR);

            while($line = fgets($errors)) {
                flush();
                $values[] = $line;
            }

            while($line = fgets($cmd)) {
                flush();
                $values[] = $line;
            }

            fclose($cmd);
        }

        return $values;
    }

}