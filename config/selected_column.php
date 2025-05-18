<?php

return [
    'tables'  => ['checkinout','userinfo'],
    'columns' => [
        'checkinout'   => ['id','USERID','LOGID','MachineId','created_at'],
        'userinfo'    => ['id','USERID','Badgenumber','name','Gender','OPHONE','ATT','INLATE','OUTEARLY','OVERTIME','email','created_at']
       
    ],
];