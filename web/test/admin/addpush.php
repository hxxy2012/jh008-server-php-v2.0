<html>
    <head>
        <title>测试推送</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
        <form action="../../admin/msgInfo/addPush" method="post">
            sendType发送类型：1所有人，2标签交，3标签并，4指定用户：
            <input type="text" name="sendType" />
            recv接收者：platform_android_versionCode_1 或 platform_ios_versionCode_1 或 uid数组：
            <input type="text" name="recv" />
            typeId：
            <input type="text" name="typeId" />
            title：
            <input type="text" name="title" />
            text：
            <input type="text" name="text" />
            url：
            <input type="text" name="url" />
            filter：
            <input type="text" name="filter" />
            publishTime：
            <input type="text" name="publishTime" />
            isSendNow：
            <input type="text" name="isSendNow" />
            <input type="submit" value="Submit">
        </form>
    </body>
</html>