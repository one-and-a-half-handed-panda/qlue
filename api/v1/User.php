<?php
/**
 * Created by PhpStorm.
 * User: ELIA SAGITA
 * Date: 10/12/2017
 * Time: 15:52
 */

class User
{
    private $id;
    private $name;
    private $email;
    private $alamat;
    private $cluster;
    private $perumahan;

    public function __construct($id, $name, $email, $alamat, $cluster, $perumahan) {
        $this->setId($id);
        $this->setName($name);
        $this->setEmail($email);
        $this->setAlamat($alamat);
        $this->setCluster($cluster);
        $this->setPerumahan($perumahan);
    }

    public function getUserDetails() {
        $details = array(
            "status"=> true,
            "message"=> "OK",
            "result"=> array(
                "name"  => $this->getName(),
                "email"=> $this->getEmail(),
                "alamat"=> $this->getAlamat(),
                "namaCluster"=> $this->getCluster(),
                "namaPerumahan"=> $this->getPerumahan()
            )
        );

        return $details;
    }

    /**
     * @param mixed $id
     */
    private function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $name
     */
    private function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param mixed $email
     */
    private function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @param mixed $alamat
     */
    private function setAlamat($alamat)
    {
        $this->alamat = $alamat;
    }

    /**
     * @param mixed $cluster
     */
    private function setCluster($cluster)
    {
        $this->cluster = $cluster;
    }

    /**
     * @param mixed $perumahan
     */
    private function setPerumahan($perumahan)
    {
        $this->perumahan = $perumahan;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    protected function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    protected function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    protected function getAlamat()
    {
        return $this->alamat;
    }

    /**
     * @return mixed
     */
    protected function getCluster()
    {
        return $this->cluster;
    }

    /**
     * @return mixed
     */
    protected function getPerumahan()
    {
        return $this->perumahan;
    }
}