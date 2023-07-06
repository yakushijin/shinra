# 単体でビルド、実行する場合

環境変数

```sh
tagName=shinra
containerName=${tagName}_run
```

ビルド/実行

```sh
docker build -t $tagName  .
imageId=`docker images | grep $tagName |awk '{print $3}'`
docker run -itd -p 443:443 -v `pwd`/src/laravel/:/var/www/laravel --privileged -t --name $containerName $imageId  /sbin/init
```

bash ログイン

```sh
docker exec -it $containerName /bin/bash
```

削除

```sh
docker stop $containerName && docker rm $containerName
docker rmi $tagName
```
