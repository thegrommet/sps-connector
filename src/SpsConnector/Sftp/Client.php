<?php
declare(strict_types=1);

namespace SpsConnector\Sftp;

use phpseclib\Net\SFTP;
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
            if (!$this->getClient()->login($username, $password)) {
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
        $result = $this->getClient()->get($remoteFile, false);
        if (is_string($result)) {
            return $result;
        }
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
        return $this->getClient()->put($remoteFile, $data);
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
        return $this->getClient()->chdir($newDir);
    }

    /**
     * @param string $remoteFile
     * @return bool
     */
    public function delete(string $remoteFile): bool
    {
        $this->login();
        return $this->getClient()->delete($remoteFile, false);
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
        $result = $this->getClient()->nlist($dir, false);
        if (is_array($result)) {
            if (!$includeSystem) {
                foreach ($result as $key => $file) {
                    if (strpos($file, '.') === 0) {
                        unset($result[$key]);
                    }
                }
            }
            return $result;
        }
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
}
