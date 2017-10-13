<?php
/**
 * Created by PhpStorm.
 * User: ELIA SAGITA
 * Date: 10/12/2017
 * Time: 11:06
 */

class unauthorizedException extends Exception {
    private $failedLogin = array(
        "status"    => false,
        "message"   => "Unauthorized",
        "result"    => array(
            "title"     => "Login Gagal",
            "message"   => "Email atau password salah"
        )
    );

    public function __construct($message = "", $code = 0, Throwable $previous = null) {
        return json_encode($this->failedLogin);
    }
}

require_once 'User.php';
require_once 'API.class.php';

class MyAPI extends API
{
    protected $User;

    private $dbhost = "localhost";
    private $dbuser = "root";
    private $dbpass = "";
    private $database = "qlue";

    public function __construct($request, $origin) {
        parent::__construct($request);
    }

    public function login() {
        if($this->method == 'POST') {
            $username = $_POST['email'];
            $password = $_POST['password'];
        }
        else {
            /*
            http_response_code(401);
            throw new unauthorizedException();
            //*/
            $username = 'eliasagitawijaya@gmail.com';
            $password = 'sengajatidakdienkripsi';
        }

        $connection = mysqli_connect($this->dbhost, $this->dbuser, $this->dbpass, $this->database);

        $query = "SELECT u.id_user,
                          u.nama AS nama_user,
                          u.email,
                          r.nama AS nama_rumah,
                          r.alamat AS alamat_rumah,
                          c.nama AS nama_cluster,
                          c.alamat AS alamat_cluster,
                          p.nama AS nama_perumahan,
                          p.alamat AS alamat_perumahan
                      FROM `user` AS u
                          JOIN `rumah_user` AS ru ON (u.id_user = ru.id_user)
                          JOIN `rumah` AS r ON (ru.id_rumah = r.id_rumah)
                          JOIN `cluster_rumah` AS cr ON (r.id_rumah = cr.id_rumah)
                          JOIN `cluster` AS c ON (cr.id_cluster = c.id_cluster)
                          JOIN `perumahan_cluster` AS pc ON (c.id_cluster = pc.id_cluster)
                          JOIN `perumahan` AS p ON (pc.id_perumahan = p.id_perumahan)
                      WHERE u.email = '$username'
                          AND u.password = '$password'";
        $results = mysqli_query($connection, $query);
        $userDetails = mysqli_fetch_assoc($results);

        mysqli_close($connection);

        if(!$userDetails) {
            http_response_code(401);
            throw new unauthorizedException();
        }
        else {
            $id         = $userDetails['id_user'];
            $name       = $userDetails['nama_user'];
            $email      = $userDetails['email'];
            $alamat     = $userDetails['alamat_perumahan'].", ".$userDetails['alamat_cluster'].", ".$userDetails['alamat_rumah'];
            $cluster    = $userDetails['nama_cluster'];
            $perumahan  = $userDetails['nama_perumahan'];

            $User = new User($id, $name, $email, $alamat, $cluster, $perumahan);
            $this->User = $User;

            return json_encode($this->User->getUserDetails());
        }
    }

    public function paidInvoices() {
        $id_user = $_GET['id_user'];

        $connection = mysqli_connect($this->dbhost, $this->dbuser, $this->dbpass, $this->database);

        $query = "SELECT t.id_tagihan,
                      t.tanggalJatuhTempo,
                      MONTHNAME(t.tanggalJatuhTempo) AS bulan_jatuh_tempo,
                      SUBSTRING(YEAR(t.tanggalJatuhTempo), 3, 2) AS tahun_jatuh_tempo,
                      DATEDIFF(CURDATE(), t.tanggalJatuhTempo) AS jatuh_tempo,
                      t.totalTagihan,
                      t.status
                  FROM `user` AS u
                      JOIN `detail_tagihan` AS dt ON (u.id_user = dt.id_user)
                      JOIN `tagihan` AS t ON (dt.id_tagihan = t.id_tagihan)
                  WHERE t.status = 1";
        $results = mysqli_query($connection, $query);

        $array = array();
        while ($invoice = mysqli_fetch_assoc($results)) {
            if($invoice['jatuh_tempo'] == 0) { $invoice['countDown'] = "today"; }
            else {
                $invoice['countDown'] = ($invoice['jatuh_tempo'] < 0) ? ($invoice['jatuh_tempo'] * -1) : $invoice['jatuh_tempo'];

                if (round($invoice['jatuh_tempo'] / 30) != 0) {
                    $invoice['countDown'] = round($invoice['jatuh_tempo'] / 30) . " months";
                } else {
                    $invoice['countDown'] .= " days";
                }

                if($invoice['jatuh_tempo'] < 0) { $invoice['countDown'] = "in ".$invoice['countDown']; }
                else { $invoice['countDown'] .= " ago"; }
            }

            $invoice['month'] = $invoice['bulan_jatuh_tempo']." ".$invoice['tahun_jatuh_tempo'];
            var_dump($invoice);

            $array[] = $invoice;
        }
        //var_dump($array);

        mysqli_close($connection);
    }
}

if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {
    $API = new MyAPI($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']);
    echo $API->processAPI();
}
catch (Exception $e) {
}