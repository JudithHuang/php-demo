<?php
require '../vendor/autoload.php';
use OSS\OssClient;
use OSS\Core\OssException;

$accessKeyId = "LTAIh39n1ZqrLbOr";
$accessKeySecret = "taRcnrsumoeI7oMpIRMUhBavsNOMwZ";
$endpoint = "http://oss-cn-shanghai.aliyuncs.com"; // "http://mts.cn-shanghai.aliyuncs.com";
// oss-cn-shanghai.aliyuncs.com
$bucket= "ivin-input-bucket";
$object = "judith.txt";
$options = array();

try {
    $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
    $ossClient->uploadFile($bucket, $object, __FILE__, $options);
} catch (OssException $e) {
    print $e->getMessage();
}