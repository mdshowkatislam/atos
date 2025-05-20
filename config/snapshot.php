<?php 


return [
     'tables'  => ['checkinout','userinfo'],
    'columns' => [
        'checkinout'   => ['id','USERID','MachineId','created_at'],
        'userinfo'    => ['id','USERID','Badgenumber','name','Gender','ATT','INLATE','OUTEARLY','OVERTIME','email','created_at']
       
    ],
    'endpoint' => env('SNAPSHOT_ENDPOINT', 'http://self-master.bidyapith.com/api/v3/attendance-store-machine'),
];

 