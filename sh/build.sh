#ビルドディレクトリクリア
rm -fR ../compile_src/*
cd ../src
tar zcf ../compile_src/laravel.tar.gz laravel/*

cd ../compile_src/
tar zxf laravel.tar.gz
rm -f laravel.tar.gz

#開発環境用のファイルを削除
if [ ${1} = "staging" -o ${1} = "prod" -o ${1} = "my" ]; then
# rm -f laravel/database/migrations/*
rm -f laravel/database/seeds/*
rm -f laravel/app/dao/Debug.php
rm -f laravel/app/Http/Controllers/DebugControllers.php
sed -i -e '/layouts.debugmode/d' laravel/resources/views/work.blade.php
sed -i -e '/debug.js/d' laravel/resources/views/work.blade.php
rm -f laravel/resources/views/layouts/debugmode.php
rm -f laravel/public/js/debug.js
fi

cd laravel/public/

mv js/app.js .

#es6→es5変換※バグるので使えない
#for i in `ls js/*.js`; do ../../../node_modules/.bin/babel ${i} -o ${i} ; echo ${i} ; done 

#ソース圧縮
# for i in `ls js/*.js`; do sed -e 's/^ *\/\/.*/ /g' ${i} | sed -z 's/\n/ /g' | sed -e 's/ \+/ /g' | sed -e 's/\/\*[^\*\/]*\*\///g' > ${i} ; done 
# for i in `ls css/*.css`; do sed -z 's/\n/ /g' ${i} | sed -e 's/ \+/ /g' | sed -e 's/\/\*[^\*\/]*\*\///g' > ${i} ; done 

mv app.js js/

cd ../../

#圧縮
tar zcf laravel.tar.gz laravel/*
