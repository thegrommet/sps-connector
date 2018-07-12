<?php
declare(strict_types=1);

namespace SpsConnector\Sftp;

use phpseclib\Net\SFTP;
use Psr\Log\LoggerInterface;
use SpsConnector\Exception\LoginFailed;
use SpsConnector\Sftp\Exception\ServerError;

/**
 * SFTP Client
 */
class Client
{
    /**
     * @var SFTP
     */
    protected $client;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    private $isLoggedIn = false;

    protected $host;
    protected $username;
    protected $password;
    protected $port;
    protected $timeout;

    public function __construct(string $host, string $username, string $password, int $port = 22, int $timeout = 10)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->port = $port;
        $this->timeout = $timeout;
    }

    /**
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function login(string $username = null, string $password = null): bool
    {
        if (!$this->isLoggedIn) {
            if ($username === null) {
                $username = $this->username;
            }
            if ($password === null) {
                $password = $this->password;
            }
            $this->logCommand('login', [$username, '***']);
            if (!$this->getClient()->login($username, $password)) {
                $this->log('login failed');
                throw new LoginFailed();
            }
            $this->isLoggedIn = true;
        }
        return $this->isLoggedIn;
    }

    /**
     * Fetch a file from the FTP server.
     *
     * @param string $remoteFile
     * @return string Contents of remote file.
     */
    public function get(string $remoteFile): string
    {
        $this->login();
        $this->logCommand('get', [$remoteFile]);
        $result = $this->getClient()->get($remoteFile, false);
        if (is_string($result)) {
            $this->log('get response length of ' . strlen($result));
            return $result;
        }
        $this->log('invalid response');
        throw new ServerError('Invalid response');
    }

    /**
     * Upload a file to the FTP server.
     *
     * @param string $remoteFile
     * @param string $data
     * @return bool Result of the operation.
     */
    public function put(string $remoteFile, string $data): bool
    {
        $this->login();
        $this->logCommand('put', [$remoteFile, sprintf('--data len: %d--', strlen($data))]);
        $result = $this->getClient()->put($remoteFile, $data);
        $this->log('put was ' . $result ? 'successful' : 'unsuccessful');
        return $result;
    }

    /**
     * Change directory on the server.
     *
     * @param string $newDir
     * @return bool Result of the operation.
     */
    public function chdir(string $newDir): bool
    {
        $this->login();
        $this->logCommand('chdir', [$newDir]);
        $result = $this->getClient()->chdir($newDir);
        $this->log('chdir was ' . $result ? 'successful' : 'unsuccessful');
        return $result;
    }

    /**
     * @param string $remoteFile
     * @return bool
     */
    public function delete(string $remoteFile): bool
    {
        $this->login();
        $this->logCommand('delete', [$remoteFile]);
        $result = $this->getClient()->delete($remoteFile, false);
        $this->log('delete was ' . $result ? 'successful' : 'unsuccessful');
        return $result;
    }

    /**
     * List the given directory.
     *
     * @param string $dir
     * @param bool $includeSystem Include files that begin with '.'
     * @return array
     */
    public function ls(string $dir = '.', bool $includeSystem = false): array
    {
        $this->login();
        $this->logCommand('ls', [$dir, $includeSystem]);
        $result = $this->getClient()->nlist($dir, false);
        if (is_array($result)) {
            $this->log('ls response count of ' . count($result));
            if (!$includeSystem) {
                foreach ($result as $key => $file) {
                    if (strpos($file, '.') === 0) {
                        unset($result[$key]);
                    }
                }
            }
            return $result;
        }
        $this->log('invalid response');
        throw new ServerError('Unable to retrieve directory listing');
    }

    public function getClient(): SFTP
    {
        if (!$this->client) {
            $this->client = new SFTP($this->host, $this->port, $this->timeout);
        }
        return $this->client;
    }

    public function setClient(SFTP $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function log(string $message, string $level = null): void
    {
        if ($this->logger) {
            if ($level) {
                $this->logger->log($level, $message);
            }
            else {
                $this->logger->info($message);
            }
        }
    }

    protected function logCommand(string $command, array $args = null): void
    {
        $this->log('CMD ' . $command . (count($args) ? ' ' . implode(' ', $args) : ''));
    }

    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;
        return $this;
    }
}
