<?php
declare (strict_types=1);

namespace think\huaweicloud\extra\common;

use Exception;
use Obs\ObsClient;
use think\facade\Request;

/**
 * 对象存储处理类
 * Class ObsFactory
 * @package think\huaweicloud\extra\common
 */
class ObsFactory
{
    /**
     * 华为云配置
     * @var array
     */
    private $option;

    /**
     * 对象存储客户端
     * @var ObsClient
     */
    private $client;

    /**
     * OssFactory constructor.
     * @param array $option
     * @param object $utils
     */
    public function __construct(array $option)
    {
        $this->option = $option;
    }

    /**
     * 创建客户端
     * @return ObsClient
     */
    private function setClient(): ObsClient
    {
        if (!empty($this->client)) {
            return $this->client;
        }
        $this->client = new ObsClient([
            'key' => $this->option['accessKeyId'],
            'secret' => $this->option['accessKeySecret'],
            'endpoint' => $this->option['obs']['endpoint']
        ]);
        return $this->client;
    }

    /**
     * 获取对象存储客户端
     * @return ObsClient
     */
    public function getClient(): ObsClient
    {
        return $this->client;
    }

    /**
     * 上传至对象存储
     * @param string $name 文件名称
     * @return string
     * @throws Exception
     */
    public function put(string $name): string
    {
        $file = Request::file($name);
        $fileName = date('Ymd') . '/' .
            uuid()->toString() . '.' .
            $file->getOriginalExtension();

        $client = $this->setClient();
        $client->putObject([
            'Bucket' => $this->option['obs']['bucket'],
            'Key' => $fileName,
            'SourceFile' => $file->getRealPath()
        ]);
        return $fileName;
    }
}