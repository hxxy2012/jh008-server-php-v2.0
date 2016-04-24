<html>
    <head>
        <title>测试</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
        <form action="../../org/actMore/modifyGroup" method="post">
            actId：
            <input type="text" name="actId" />
            <br><br>
            groups[0][groupId]
            <input type="text" name="groups[0][groupId]" />
            groups[0][groupName]
            <input type="text" name="groups[0][groupName]" />
            groups[0][userIds][0]
            <input type="text" name="groups[0][userIds][0]" />
            groups[0][userIds][1]
            <input type="text" name="groups[0][userIds][1]" />
            <br><br>
            groups[1][groupId]
            <input type="text" name="groups[1][groupId]" />
            groups[1][groupName]
            <input type="text" name="groups[1][groupName]" />
            groups[1][userIds][0]
            <input type="text" name="groups[1][userIds][0]" />
            groups[1][userIds][1]
            <input type="text" name="groups[1][userIds][1]" />
            <input type="submit" value="Submit">
        </form>
    </body>
</html>