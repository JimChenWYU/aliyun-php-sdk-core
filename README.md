# aliyun-php-sdk-core

阿里云PHP SDK 核心库

fork by [here](https://github.com/aliyun/aliyun-openapi-php-sdk/tree/master/aliyun-php-sdk-core)

## Requirements

- PHP 5.3+

## Installing

```shell
$ composer require jimchen/aliyun-php-sdk-core:~1.0
```

## Usage

以[视频点播-上传](https://help.aliyun.com/document_detail/61069.html?spm=a2c4g.11186623.6.788.5b3475d9cMm7cY)为例，展示如何使用：
```php
// 1. 初始化客户端
// 官方中需要`require_once './aliyun-php-sdk/aliyun-php-sdk-core/Config.php';`
// 这里不需要，因为都在 bootstrap.php 中通过 composer 进行了自动加载

use JimChen\AliyunCore\DefaultAcsClient;

function init_vod_client($accessKeyId, $accessKeySecret) {
    $regionId = 'cn-shanghai';  // 点播服务所在的Region，国内请填cn-shanghai，不要填写别的区域
    $profile = DefaultProfile::getProfile($regionId, $accessKeyId, $accessKeySecret);
    return new DefaultAcsClient($profile);
}

// 2. 获取视频上传地址和凭证
function create_upload_video($client) {
    $request = new CreateUploadVideoRequest();
    $request->setTitle("视频标题");        // 视频标题(必填参数)
    $request->setFileName("文件名称.mov"); // 视频源文件名称，必须包含扩展名(必填参数)
    $request->setDescription("视频描述");  // 视频源文件描述(可选)
    $request->setCoverURL("http://img.alicdn.com/tps/TB1qnJ1PVXXXXXCXXXXXXXXXXXX-700-700.png"); // 自定义视频封面(可选)
    $request->setTags("标签1,标签2"); // 视频标签，多个用逗号分隔(可选)
    $request->setAcceptFormat('JSON');
    return $client->getAcsResponse($request);
}
try {
    $client = init_vod_client('<您的AccessKeyId>', '<您的AccessKeySecret>');
    $uploadInfo = create_upload_video($client);
    var_dump($uploadInfo);
} catch (Exception $e) {
    print $e->getMessage()."\n";
}

// 3. 刷新视频上传凭证
function refresh_upload_video($client, $videoId) {
    $request = new RefreshUploadVideoRequest();
    $request->setVideoId($videoId);
    $request->setAcceptFormat('JSON');
    return $client->getAcsResponse($request);
}
try {
    $client = init_vod_client('<您的AccessKeyId>', '<您的AccessKeySecret>');
    $refreshInfo = refresh_upload_video($client, '您的videoId');
    var_dump($refreshInfo);
} catch (Exception $e) {
    print $e->getMessage()."\n";
}

// 4. 获取图片上传地址和凭证
function create_upload_image($client, $imageType, $imageExt) {
    $request = new CreateUploadImageRequest();
    $request->setImageType($imageType);
    $request->setImageExt($imageExt);
    $request->setAcceptFormat('JSON');
    return $client->getAcsResponse($request);
}
try {
    $client = init_vod_client('<您的AccessKeyId>', '<您的AccessKeySecret>');
    $imageInfo = create_upload_image($client, 'cover', 'jpg');
    var_dump($imageInfo);
} catch (Exception $e) {
    print $e->getMessage()."\n";
}
```

## License

Apache License 2.0