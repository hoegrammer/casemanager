<?php

$reports = array 
	(
	array
		(
		'name'=>'clcount',
		'title'=>'Clients',
		'sql'=>'select count(*) as Clients from ( select cl.id_client as idcl from lcm_client as cl left join lcm_case_client_org as cco on cco.id_client = cl.id_client left join lcm_followup as fu on fu.id_case = cco.id_case where %&AGE&% > \'%&DATESTART&%\' and %&AGE&% < \'%&DATEEND&%\' group by cl.id_client) as test %&FUND&% where %&WHERE&%' ,
		'note'=>'Count of all clients with case work entered onto the system within the time period given (regardless of whether or not they have a case or any case activity during this period).',
		'fund'=>', (select cl.id_client as idcl from lcm_client as cl left join lcm_keyword_client as kcl on kcl.id_client = cl.id_client left join lcm_keyword as k on k.id_keyword = kcl.id_keyword where k.id_group = 24  and k.id_keyword=%&FUNDER&% ) as fund',
		'where'=>'test.idcl = fund.idcl',
		'new'=>'cl.date_creation',
		'old'=>'fu.date_start'
		),
	array
		(
		'name'=>'cluser',
		'title'=>'Clients, by User',
		'sql'=>'select count(*) as Total %&XTAB&% from ( select cl.id_client as idcl, a.id_author as ida, a.name_first as name from lcm_client as cl left join lcm_case_client_org as cco on cco.id_client = cl.id_client left join lcm_followup as fu on fu.id_case = cco.id_case left join lcm_case_author as ca on cco.id_case = ca.id_case left join lcm_author as a on ca.id_author = a.id_author left join lcm_case as c on c.id_case = cco.id_case where %&AGE&% > \'%&DATESTART&%\' and %&AGE&% < \'%&DATEEND&%\' group by cl.id_client ) as test %&FUND&% where %&WHERE&%;',
		'xtab'=>"SELECT CONCAT(', SUM(IF(ida = \"',a.id_author,'\",1,0))AS\"',CONCAT(a.name_first,\" \",a.name_last),'\"') FROM lcm_author as a left join lcm_followup as fu on fu.id_author = a.id_author where fu.date_start > '%&DATESTART&%' and fu.date_start < '%&DATEEND&%' group by a.id_author",
		'note'=>'The devision of clients between users for this report is calcualted based on the owner first case opened for that client. Any clients for whom a case has not been opened will not be listed.',
		'fund'=>', (select cl.id_client as idcl from lcm_client as cl left join lcm_keyword_client as kcl on kcl.id_client = cl.id_client left join lcm_keyword as k on k.id_keyword = kcl.id_keyword where k.id_group = 24 and k.id_keyword=%&FUNDER&%  ) as fund',
		'where'=>'test.idcl = fund.idcl',
		'new'=>'cl.date_creation',
		'old'=>'fu.date_start'
		),
	array
		(
		'name'=>'clstat',
		'title'=>'Clients, by Status and User',
		'sql'=>'select test.stat as Status, count(*) as Total %&XTAB&% from ( select k.title as stat, cl.id_client as idcl, a.id_author as ida, a.name_first as name from lcm_client as cl left join lcm_case_client_org as cco on cco.id_client = cl.id_client left join lcm_followup as fu on fu.id_case = cco.id_case left join lcm_case_author as ca on cco.id_case = ca.id_case left join lcm_author as a on ca.id_author = a.id_author left join lcm_keyword_client as kcl on cl.id_client = kcl.id_client left join lcm_keyword as k on kcl.id_keyword = k.id_keyword where k.id_group = 19 and %&AGE&% > \'%&DATESTART&%\' and %&AGE&% < \'%&DATEEND&%\' group by cl.id_client) as test %&FUND&% where %&WHERE&% group by test.stat',
		'note'=>'Any clients without a status set will not be counted in these figures.',
		'fund'=>', (select cl.id_client as idcl from lcm_client as cl left join lcm_keyword_client as kcl on kcl.id_client = cl.id_client left join lcm_keyword as k on k.id_keyword = kcl.id_keyword where k.id_group = 24 and k.id_keyword=%&FUNDER&%  ) as fund',
		'where'=>'test.idcl = fund.idcl',
		'new'=>'cl.date_creation',
		'old'=>'fu.date_start',
		'xtab'=>"SELECT CONCAT(', SUM(IF(ida = \"',a.id_author,'\",1,0))AS\"',CONCAT(a.name_first,\" \",a.name_last),'\"') FROM lcm_author as a left join lcm_followup as fu on fu.id_author = a.id_author where fu.date_start > '%&DATESTART&%' and fu.date_start < '%&DATEEND&%' group by a.id_author"
		),
	array
		(
		'name'=>'clnat',
		'title'=>'Clients, by Nationality and User',
		'sql'=>'select test.stat as Status, count(*) as Total %&XTAB&% from ( select k.title as stat, cl.id_client as idcl, a.id_author as ida, a.name_first as name from lcm_client as cl left join lcm_case_client_org as cco on cco.id_client = cl.id_client left join lcm_followup as fu on fu.id_case = cco.id_case left join lcm_case_author as ca on cco.id_case = ca.id_case left join lcm_author as a on ca.id_author = a.id_author left join lcm_keyword_client as kcl on cl.id_client = kcl.id_client left join lcm_keyword as k on kcl.id_keyword = k.id_keyword where k.id_group = 20 and %&AGE&% > \'%&DATESTART&%\' and %&AGE&% < \'%&DATEEND&%\' group by cl.id_client) as test %&FUND&% where %&WHERE&% group by test.stat',
		'note'=>'Any clients without a nationality set will not be counted in these figures.',
		'fund'=>', (select cl.id_client as idcl from lcm_client as cl left join lcm_keyword_client as kcl on kcl.id_client = cl.id_client left join lcm_keyword as k on k.id_keyword = kcl.id_keyword where k.id_group = 24 and k.id_keyword=%&FUNDER&%  ) as fund',
		'xtab'=>"SELECT CONCAT(', SUM(IF(ida = \"',a.id_author,'\",1,0))AS\"',CONCAT(a.name_first,\" \",a.name_last),'\"') FROM lcm_author as a left join lcm_followup as fu on fu.id_author = a.id_author where fu.date_start > '%&DATESTART&%' and fu.date_start < '%&DATEEND&%' group by a.id_author",
		'where'=>'test.idcl = fund.idcl',
		'new'=>'cl.date_creation',
		'old'=>'fu.date_start',
		),
	array
		(
		'name'=>'clnatsex',
		'title'=>'Clients, by Nationality and Gender',
		'sql'=>'select test.stat as Status, count(*) as Total, sum(if(test.gen="male",1,0)) as Male, sum(if(test.gen="female",1,0)) as Female, sum(if(test.gen="unknown",1,0)) as "Not Recorded" from ( select cl.gender as gen, k.title as stat, cl.id_client as idcl, a.id_author as ida, a.name_first as name from lcm_client as cl left join lcm_case_client_org as cco on cco.id_client = cl.id_client left join lcm_case_author as ca on cco.id_case = ca.id_case left join lcm_author as a on ca.id_author = a.id_author left join lcm_followup as fu on fu.id_case = cco.id_case left join lcm_keyword_client as kcl on cl.id_client = kcl.id_client left join lcm_keyword as k on kcl.id_keyword = k.id_keyword where k.id_group = 20 and %&AGE&% > \'%&DATESTART&%\' and %&AGE&% < \'%&DATEEND&%\' group by cl.id_client) as test %&FUND&% where %&WHERE&% group by test.stat',
		'note'=>'Any clients without a nationality set will not be counted in these figures.',
		'fund'=>', (select cl.id_client as idcl from lcm_client as cl left join lcm_keyword_client as kcl on kcl.id_client = cl.id_client left join lcm_keyword as k on k.id_keyword = kcl.id_keyword where k.id_group = 24 and k.id_keyword=%&FUNDER&%  ) as fund',
		'where'=>'test.idcl = fund.idcl',
		'new'=>'cl.date_creation',
		'old'=>'fu.date_start',
		),
	array
		(
		'name'=>'clage',
		'title'=>'Clients, by Age (SCC bands)',
		'sql'=>'select SUM(IF((test.cldob>-1 &&test.cldob <=19),1,0)) as "Under 20", SUM(IF((test.cldob>19 && test.cldob<=59),1,0)) as "19 to 59", SUM(IF((test.cldob >59 && test.cldob <1000),1,0)) as "Over 60", SUM(IF((test.cldob >=1000 || test.cldob < 0 ),1,0)) as "Unrecorded / Error" from (select cl.id_client as idc , (YEAR(\'%&DATESTART&%\')-YEAR(cl.date_birth)-(RIGHT(\'%&DATESTART&%\',5)<RIGHT(cl.date_birth,5))) as cldob from lcm_client as cl left join lcm_case_client_org as cco on cco.id_client = cl.id_client left join lcm_followup as fu on fu.id_case = cco.id_case where %&AGE&% > \'%&DATESTART&%\' and %&AGE&% < \'%&DATEEND&%\' group by cl.id_client) as test %&FUND&% where %&WHERE&%',
		'note'=>'A clients age is calculated from their date of birth, and the start date for the query, to the nearest month. The "Unrecorded/Error" column totals all clients who have not had a date of birth recorded on their file, and any clients with a unrealisitc date of birth (usually cased by users entering "0085" not "1985", or somesuch).',
		'fund'=>', (select cl.id_client as idcl from lcm_client as cl left join lcm_keyword_client as kcl on kcl.id_client = cl.id_client left join lcm_keyword as k on k.id_keyword = kcl.id_keyword where k.id_group = 24 and k.id_keyword=%&FUNDER&%  ) as fund',
		'where'=>'test.idc = fund.idcl',
		'new'=>'cl.date_creation',
		'old'=>'fu.date_start',
		),
	array
		(
		'name'=>'clageII',
		'title'=>'Clients, by Age (BND bands)',
		'sql'=>'select SUM(IF((test.cldob>-1 &&test.cldob <=15),1,0)) as "Under 16", SUM(IF((test.cldob>15 && test.cldob<=24),1,0)) as "16 to 24", SUM(IF((test.cldob >24 && test.cldob <=34),1,0)) as "25 to 34", SUM(IF((test.cldob >34 && test.cldob <=49),1,0)) as "35 to 49", SUM(IF((test.cldob >49 && test.cldob <=64),1,0)) as "50 to 64", SUM(IF((test.cldob >64 && test.cldob <=74),1,0)) as "65 to 74", SUM(IF((test.cldob >74 && test.cldob <=1000),1,0)) as "75 and Over", SUM(IF((test.cldob >=1000 || test.cldob < 0 ),1,0)) as "Unrecorded / Error" from (select cl.id_client as idc , (YEAR(\'%&DATESTART&%\')-YEAR(cl.date_birth)-(RIGHT(\'%&DATESTART&%\',5)<RIGHT(cl.date_birth,5))) as cldob from lcm_client as cl left join lcm_case_client_org as cco on cco.id_client = cl.id_client left join lcm_followup as fu on fu.id_case = cco.id_case where %&AGE&% > \'%&DATESTART&%\' and %&AGE&% < \'%&DATEEND&%\' group by cl.id_client) as test %&FUND&% where %&WHERE&%',
		'note'=>'A clients age is calculated from their date of birth, and the start date for the query, to the nearest month. The "Unrecorded/Error" column totals all clients who have not had a date of birth recorded on their file, and any clients with a unrealisitc date of birth (usually cased by users entering "0085" not "1985", or somesuch).',
		'fund'=>', (select cl.id_client as idcl from lcm_client as cl left join lcm_keyword_client as kcl on kcl.id_client = cl.id_client left join lcm_keyword as k on k.id_keyword = kcl.id_keyword where k.id_group = 24 and k.id_keyword=%&FUNDER&%  ) as fund',
		'where'=>'test.idc = fund.idcl',
		'new'=>'cl.date_creation',
		'old'=>'fu.date_start',
		),
	array
		(
		'name'=>'cldis',
		'title'=>'Clients, by Disability',
		'sql'=>'select test.stat as Disabled, count(*) as Total from ( select k.title as stat, cl.id_client as idcl from lcm_client as cl left join lcm_case_client_org as cco on cco.id_client = cl.id_client left join lcm_followup as fu on fu.id_case = cco.id_case left join lcm_keyword_client as kcl on cl.id_client = kcl.id_client left join lcm_keyword as k on kcl.id_keyword = k.id_keyword where k.id_group = 23 and %&AGE&% > \'%&DATESTART&%\' and %&AGE&% < \'%&DATEEND&%\' group by cl.id_client) as test %&FUND&% where %&WHERE&% group by test.stat',
		'fund'=>', (select cl.id_client as idcl from lcm_client as cl left join lcm_keyword_client as kcl on kcl.id_client = cl.id_client left join lcm_keyword as k on k.id_keyword = kcl.id_keyword where k.id_group = 24 and k.id_keyword=%&FUNDER&%  ) as fund',
		'where'=>'test.idcl = fund.idcl',
		'new'=>'cl.date_creation',
		'old'=>'fu.date_start',
		),
	array
		(
		'name'=>'clintv',
		'title'=>'Clients, by Intervention',
		'sql'=>'select if(test.stat2=1, "Ongoing","One-Off") as Intervention, count(test.clid) as Clients from (select bit_or(if(c.status="open"||"closed",1,0)) as stat2, cl.id_client as clid, c.id_case as cid, c.status as stat from lcm_client cl left join lcm_case_client_org as cco on cco.id_client = cl.id_client left join lcm_case as c on c.id_case = cco.id_case left join lcm_followup as fu on fu.id_case = cco.id_case where %&AGE&% > \'%&DATESTART&%\' and %&AGE&% group by cl.id_client ) as test %&FUND&% where %&WHERE&% group by test.stat2',
		'note'=>'',
		'fund'=>', (select cl.id_client as idcl from lcm_client as cl left join lcm_keyword_client as kcl on kcl.id_client = cl.id_client left join lcm_keyword as k on k.id_keyword = kcl.id_keyword where k.id_group = 24 and k.id_keyword=%&FUNDER&%  ) as fund',
		'where'=>'test.clid = fund.idcl',
		'new'=>'cl.date_creation',
		'old'=>'fu.date_start'
		),
	array
		(
		'name'=>'ccount',
		'title'=>'Cases, by User',
		'sql'=>'select count(*) as Cases %&XTAB&% from (select c.id_case as idc, a.id_author as ida from lcm_case as c left join lcm_case_author as ca on c.id_case = ca.id_case left join lcm_author as a on ca.id_author = a.id_author left join lcm_followup as fu on fu.id_case = c.id_case where %&AGE&% > \'%&DATESTART&%\' and %&AGE&% < \'%&DATEEND&%\' group by c.id_case) as test %&FUND&% where %&WHERE&%',
		'xtab'=>"SELECT CONCAT(', SUM(IF(ida = \"',a.id_author,'\",1,0))AS\"',CONCAT(a.name_first,\" \",a.name_last),'\"') FROM lcm_author as a left join lcm_followup as fu on fu.id_author = a.id_author where fu.date_start > '%&DATESTART&%' and fu.date_start < '%&DATEEND&%' group by a.id_author",
		'fund'=>', (select cco.id_case as idc from lcm_client as cl left join lcm_case_client_org as cco on cl.id_client = cco.id_client left join lcm_keyword_client as kcl on kcl.id_client = cl.id_client left join lcm_keyword as k on k.id_keyword = kcl.id_keyword where k.id_group = 24 and k.id_keyword=%&FUNDER&% ) as fund',
		'where'=>'test.idc = fund.idc',
		'note'=>'Count of all cases created during given period (regardless of when their client was registered).',
		'new'=>'c.date_creation',
		'old'=>'fu.date_start'
		),
	array
		(
		'name'=>'cmat',
		'title'=>'Cases, by Matter and User',
		'sql'=>'select test.stat as Matter, count(*) as Total %&XTAB&% from (select k.title as stat, c.id_case as idc, a.id_author as ida from lcm_case as c left join lcm_case_author as ca on c.id_case = ca.id_case left join lcm_author as a on ca.id_author = a.id_author left join lcm_keyword_case as kc on kc.id_case = c.id_case left join lcm_keyword as k on k.id_keyword = kc.id_keyword left join lcm_followup as fu on fu.id_case = c.id_case where k.id_group = 27 and %&AGE&% > \'%&DATESTART&%\' and %&AGE&% < \'%&DATEEND&%\' group by c.id_case) as test %&FUND&% where %&WHERE&% group by Matter',
		'xtab'=>"SELECT CONCAT(', SUM(IF(ida = \"',a.id_author,'\",1,0))AS\"',CONCAT(a.name_first,\" \",a.name_last),'\"') FROM lcm_author as a left join lcm_followup as fu on fu.id_author = a.id_author where fu.date_start > '%&DATESTART&%' and fu.date_start < '%&DATEEND&%' group by a.id_author",
		'fund'=>', (select cco.id_case as idc from lcm_client as cl left join lcm_case_client_org as cco on cl.id_client = cco.id_client left join lcm_keyword_client as kcl on kcl.id_client = cl.id_client left join lcm_keyword as k on k.id_keyword = kcl.id_keyword where k.id_group = 24 and k.id_keyword=%&FUNDER&% ) as fund',
		'where'=>'test.idc = fund.idc',
		'note'=>'Cases listed by Creating user and Matter type.',
		'new'=>'c.date_creation',
		'old'=>'fu.date_start'
		),
	array
		(
		'name'=>'cint',
		'title'=>'Cases, by Intervention',
		'sql'=>'select if(test.stat="open"||"closed","Ongoing","One-off") as Intervention, count(test.idc) as Cases from (select c.id_case as idc, c.status as stat from lcm_case as c left join lcm_followup as fu on fu.id_case = c.id_case %&FUND&% where %&WHERE&% and %&AGE&% > \'%&DATESTART&%\' and %&AGE&% < \'%&DATEEND&%\' group by c.id_case) as test group by Intervention',
		'fund'=>', (select cco.id_case as idc from lcm_client as cl left join lcm_case_client_org as cco on cl.id_client = cco.id_client left join lcm_keyword_client as kcl on kcl.id_client = cl.id_client left join lcm_keyword as k on k.id_keyword = kcl.id_keyword where k.id_group = 24 and k.id_keyword=%&FUNDER&% ) as fund',
		'where'=>'c.id_case = fund.idc',
		'note'=>'Cases listed by Intervention (Open or Closed = Ongoing)',
		'new'=>'c.date_creation',
		'old'=>'fu.date_start'
		),
	array
		(
		'name'=>'cnatsex',
		'title'=>'Cases, by the Nationality and Gender of their Clients',
		'sql'=>'select test2.Nationality as Nationality, sum(if(test2.Gender="male",1,0)) as Male, sum(if(test2.Gender="female",1,0)) as Female, sum(if(test2.Gender="unknown",1,0)) as "Not Recorded" from (select if(test.title!="",test.title,"Not Recorded") as Nationality, if(cl.gender!="",cl.gender,"unknown") as Gender  from lcm_case as c left join lcm_followup as fu on fu.id_case = c.id_case left join lcm_case_client_org as cco on cco.id_case = c.id_case left join lcm_client as cl on cl.id_client = cco.id_client left join (select kwcl.id_client as id_client, kw.title as title from lcm_keyword_client as kwcl left join lcm_keyword as kw on kw.id_keyword = kwcl.id_keyword where kw.id_group = 20) as test on test.id_client = cl.id_client %&FUND&% where %&WHERE&% and %&AGE&% > \'%&DATESTART&%\' and %&AGE&% < \'%&DATEEND&%\' group by c.id_case) as test2 group by test2.Nationality',
		'fund'=>', (select cco.id_case as idc from lcm_client as cl left join lcm_case_client_org as cco on cl.id_client = cco.id_client left join lcm_keyword_client as kcl on kcl.id_client = cl.id_client left join lcm_keyword as k on k.id_keyword = kcl.id_keyword where k.id_group = 24 and k.id_keyword=%&FUNDER&% ) as fund',
		'where'=>'c.id_case = fund.idc',
		'note'=>'Cases listed by the Nationality and Gender of the Client. Cases attached to an Agency rather than a Client are totalled under "Not Recorded".',
		'new'=>'c.date_creation',
		'old'=>'fu.date_start'
		),
//	array
//		(
//		'name'=>'clcount',
//		'title'=>'New Client Count',
//		'sql'=>'select count(*) as Clients from ( select cl.id_client as idcl from lcm_client as cl where cl.date_creation > \'%&DATESTART&%\' and cl.date_creation < \'%&DATEEND&%\' ) as test %&FUND&% where %&WHERE&%' ,
//		'note'=>'Count of all clients registered on the system during given period (regardless of whether or not they have a case or any case activity during this period).',
//		'fund'=>', (select cl.id_client as idcl from lcm_client as cl left join lcm_keyword_client as kcl on kcl.id_client = cl.id_client left join lcm_keyword as k on k.id_keyword = kcl.id_keyword where k.id_group = 24  and k.id_keyword=%&FUNDER&% ) as fund',
//		'where'=>'test.idcl = fund.idcl'
//		),

	array
		(
		'name'=>'newfu',
		'title'=>'Work, by User',
		'sql'=>'select count(*) as "Work Items" %&XTAB&% from (select fu.id_followup as idfu, a.id_author as ida from lcm_followup as fu left join lcm_author as a on fu.id_author = a.id_author  where fu.date_start > \'%&DATESTART&%\' and fu.date_start < \'%&DATEEND&%\' ) as test %&FUND&% where %&WHERE&%',
		'xtab'=>"SELECT CONCAT(', SUM(IF(ida = \"',a.id_author,'\",1,0))AS\"',CONCAT(a.name_first,\" \",a.name_last),'\"') FROM lcm_author as a left join lcm_followup as fu on fu.id_author = a.id_author where (fu.date_start > '%&DATESTART&%' and fu.date_start < '%&DATEEND&%') group by a.id_author ",
		'fund'=>', (select fu.id_followup as idfu from lcm_client as cl left join lcm_case_client_org as cco on cl.id_client = cco.id_client left join lcm_followup as fu on cco.id_case = fu.id_case left join lcm_keyword_client as kcl on kcl.id_client = cl.id_client left join lcm_keyword as k on k.id_keyword = kcl.id_keyword where k.id_group = 24 and k.id_keyword=%&FUNDER&% ) as fund',
		'where'=>'test.idfu = fund.idfu'
		),
	array
		(
		'name'=>'newfutype',
		'title'=>'Work, by Type and User',
		'sql'=>'select test.type as "Work Type", count(*) as Total %&XTAB&% from (select fu.id_followup as idfu, kw.title as type, a.id_author as ida from lcm_followup as fu left join lcm_author as a on fu.id_author = a.id_author left join lcm_keyword as kw on fu.type = kw.name where fu.date_start > \'%&DATESTART&%\' and fu.date_start < \'%&DATEEND&%\' ) as test %&FUND&% where %&WHERE&% group by test.type',
		'xtab'=>"SELECT CONCAT(', SUM(IF(ida = \"',a.id_author,'\",1,0))AS\"',CONCAT(a.name_first,\" \",a.name_last),'\"') FROM lcm_author as a left join lcm_followup as fu on fu.id_author = a.id_author where (fu.date_start > '%&DATESTART&%' and fu.date_start < '%&DATEEND&%') group by a.id_author ",
		'fund'=>', (select fu.id_followup as idfu from lcm_client as cl left join lcm_case_client_org as cco on cl.id_client = cco.id_client left join lcm_followup as fu on cco.id_case = fu.id_case left join lcm_keyword_client as kcl on kcl.id_client = cl.id_client left join lcm_keyword as k on k.id_keyword = kcl.id_keyword where k.id_group = 24 and k.id_keyword=%&FUNDER&% ) as fund',
		'where'=>'test.idfu = fund.idfu'
		),
	array
		(
		'name'=>'newfutypeII',
		'title'=>'Work, by Type and User (Restricted to Client Contact only)',
		'sql'=>'select test.type as "Work Type", count(*) as Total %&XTAB&% from (select fu.id_followup as idfu, kw.title as type, a.id_author as ida from lcm_followup as fu left join lcm_author as a on fu.id_author = a.id_author left join lcm_keyword as kw on fu.type = kw.name where kw.description = "clicon" and fu.date_start > \'%&DATESTART&%\' and fu.date_start < \'%&DATEEND&%\' ) as test %&FUND&% where %&WHERE&% group by test.type',
		'xtab'=>"SELECT CONCAT(', SUM(IF(ida = \"',a.id_author,'\",1,0))AS\"',CONCAT(a.name_first,\" \",a.name_last),'\"') FROM lcm_author as a left join lcm_followup as fu on fu.id_author = a.id_author where (fu.date_start > '%&DATESTART&%' and fu.date_start < '%&DATEEND&%') group by a.id_author ",
		'fund'=>', (select fu.id_followup as idfu from lcm_client as cl left join lcm_case_client_org as cco on cl.id_client = cco.id_client left join lcm_followup as fu on cco.id_case = fu.id_case left join lcm_keyword_client as kcl on kcl.id_client = cl.id_client left join lcm_keyword as k on k.id_keyword = kcl.id_keyword where k.id_group = 24 and k.id_keyword=%&FUNDER&% ) as fund',
		'where'=>'test.idfu = fund.idfu'
		),
	array
		(
		'name'=>'newfutypeIII',
		'title'=>'Work, by Client Postcode (Restricted to Client Contact only)',

//		'sql'=>'select CONCAT_WS(\' \',LEFT(filtered.yes,CHAR_LENGTH(filtered.yes)-3),SUBSTRING(filtered.yes,CHAR_LENGTH(filtered.yes)-2,1)) as Stub, count(filtered.yes)as Total from (select IF(codes.yes REGEXP \'^[a-zA-Z]{1,2}[0-9]{1,3}[a-zA-Z]{2}$\',codes.yes,IF(SUBSTRING_INDEX(codes.yes,\',\',-1)REGEXP \'^[a-zA-Z]{1,2}[0-9]{1,3}[a-zA-Z]{2}$\', SUBSTRING_INDEX(codes.yes,\',\',-1), IF(LEFT(codes.yes,CHAR_LENGTH(codes.yes)-1) REGEXP \'[a-zA-Z]{1,2}[0-9]{1,3}[a-zA-Z]{2}\', LEFT(codes.yes,CHAR_LENGTH(codes.yes)-1) ,concat(\'? Best Effort: \',codes.yes)))) as yes from (SELECT CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(co.value,\' \',-2),\' \',1),SUBSTRING_INDEX(co.value,\' \',-1)) as yes, co.value as no FROM lcm_contact as co where co.type_person="client" UNION SELECT SUBSTRING_INDEX(co.value,\' \',-1) as yes, co.value as no FROM lcm_contact AS co WHERE co.type_person="client") AS codes WHERE codes.yes regexp \'[a-zA-Z]{1,2}[0-9]{2,3}[a-zA-Z]{2}\') as filtered group by Stub'
		'sql'=>'






SELECT 
	CONCAT_WS(\' \',LEFT(filtered.yes,CHAR_LENGTH(filtered.yes)-3),SUBSTRING(filtered.yes,CHAR_LENGTH(filtered.yes)-2,1)) as Stub,
	COUNT(filtered.yes) as Total
FROM 
	(
	SELECT 
		IF(codes.yes REGEXP \'^[a-zA-Z]{1,2}[0-9]{1,3}[a-zA-Z]{2}$\',codes.yes,
			IF(SUBSTRING_INDEX(codes.yes,\',\',-1)REGEXP \'^[a-zA-Z]{1,2}[0-9]{1,3}[a-zA-Z]{2}$\', SUBSTRING_INDEX(codes.yes,\',\',-1), 
				IF(LEFT(codes.yes,CHAR_LENGTH(codes.yes)-1) REGEXP \'[a-zA-Z]{1,2}[0-9]{1,3}[a-zA-Z]{2}\',
					LEFT(codes.yes,CHAR_LENGTH(codes.yes)-1) ,concat(\' <b>?</b>: \',codes.yes)))) as yes,
		codes.id as id,
		kw.name as type
	FROM 
		(
		SELECT 
			CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(co.value,\' \',-2),\' \',1),SUBSTRING_INDEX(co.value,\' \',-1)) as yes, 
			co.value as no,
			id_of_person as id
		FROM 
			lcm_contact as co 
		WHERE
			co.type_person="client" 
		UNION SELECT 
			SUBSTRING_INDEX(co.value,\' \',-1) as yes, 
			co.value as no,
			id_of_person as id
		FROM 
			lcm_contact AS co 
		WHERE 
			co.type_person="client"
		) AS codes
	LEFT JOIN
		lcm_case_client_org as cco on codes.id = cco.id_client
	LEFT JOIN
		lcm_followup as fu ON fu.id_case = cco.id_case
	LEFT JOIN
		lcm_keyword as kw on fu.type = kw.name
	WHERE 
		codes.yes regexp \'[a-zA-Z]{1,2}[0-9]{2,3}[a-zA-Z]{2}\'
	AND
		kw.description = \'clicon\'
	AND
		fu.date_start > \'%&DATESTART&%\'
	AND
		fu.date_start <= \'%&DATEEND&%\'
	) as filtered 
GROUP BY
	Stub
	
'
		),
	array
		(
		'name'=>'outcome',
		'title'=>'Work, by Outcome and Amounts',
		'sql'=>'select k.title as "Outcome Type", sum(1) as "Outcomes Recorded", sum(fu.outcome_amount) as "Total Amount" from lcm_followup as fu left join lcm_keyword as k on fu.outcome = k.id_keyword %&FUND&% where %&WHERE&% and outcome != "" and fu.date_start > \'%&DATESTART&%\' and fu.date_start < \'%&DATEEND&%\' group by k.title UNION select "Only Amount", sum(1), sum(fu.outcome_amount) from lcm_followup as fu %&FUND&% where %&WHERE&% and fu.outcome = "" and fu.outcome_amount > 0 and fu.date_start > \'%&DATESTART&%\' and fu.date_start <= \'%&DATEEND&%\' UNION select "Total", sum(1), sum(fu.outcome_amount) from lcm_followup as fu %&FUND&% where %&WHERE&% and (fu.outcome_amount > 0 or fu.outcome != "") and fu.date_start > \'%&DATESTART&%\' and fu.date_start <= \'%&DATEEND&%\'',
		'fund'=>', (select fu.id_followup as idfu from lcm_client as cl left join lcm_case_client_org as cco on cl.id_client = cco.id_client left join lcm_followup as fu on cco.id_case = fu.id_case left join lcm_keyword_client as kcl on kcl.id_client = cl.id_client left join lcm_keyword as k on k.id_keyword = kcl.id_keyword where k.id_group = 24 and k.id_keyword=%&FUNDER&% ) as fund',
		'where'=>'fu.id_followup = fund.idfu',
		'note'=>''
		),
	array
		(
		'name'=>'newfuref',
		'title'=>'Work, by Referals Made',
		'sql'=>'select test.type as "Referal to", count(*) as Total from (select fu.id_followup as idfu, kw.title as type, a.id_author as ida from lcm_followup as fu left join lcm_author as a on fu.id_author = a.id_author left join lcm_keyword_followup as  kwfu on fu.id_followup = kwfu.id_followup left join lcm_keyword as kw on kwfu.id_keyword = kw.id_keyword where kw.id_group = 18 and fu.date_start > \'%&DATESTART&%\' and fu.date_start < \'%&DATEEND&%\' ) as test %&FUND&% where %&WHERE&% group by test.type',
		'xtab'=>"SELECT CONCAT(', SUM(IF(ida = \"',a.id_author,'\",1,0))AS\"',CONCAT(a.name_first,\" \",a.name_last),'\"') FROM lcm_author as a left join lcm_followup as fu on fu.id_author = a.id_author where (fu.date_start > '%&DATESTART&%' and fu.date_start < '%&DATEEND&%') group by a.id_author ",
		'fund'=>', (select fu.id_followup as idfu from lcm_client as cl left join lcm_case_client_org as cco on cl.id_client = cco.id_client left join lcm_followup as fu on cco.id_case = fu.id_case left join lcm_keyword_client as kcl on kcl.id_client = cl.id_client left join lcm_keyword as k on k.id_keyword = kcl.id_keyword where k.id_group = 24 and k.id_keyword=%&FUNDER&% ) as fund',
		'where'=>'test.idfu = fund.idfu'
		),
	array
		(
		'name'=>'workersII',
		'title'=>'Work, by User and Client',
		'sql'=>'select (CONCAT(a.name_first," ",a.name_last)) as "User", (CONCAT("<a href=\'client_det.php?client=",cl.id_client,"\'>",cl.name_first," ",cl.name_last,"</a>")) as "Client", (CONCAT("<a href=\'case_det.php?case=",c.id_case,"\'>",kw.title,"</a>")) as "Case", (CONCAT("<a href=\'fu_det.php?followup=",fu.id_followup,"\'>",kwII.title,"</a>")) as Work from lcm_followup as fu left join lcm_author as a on a.id_author = fu.id_author left join lcm_case as c on c.id_case = fu.id_case left join lcm_case_client_org as cco on cco.id_case = c.id_case left join lcm_client as cl on cl.id_client = cco.id_client left join lcm_keyword_case as kwc on kwc.id_case = c.id_case left join lcm_keyword as kw on kw.id_keyword = kwc.id_keyword left join lcm_keyword as kwII on kwII.name = fu.type %&FUND&% where %&WHERE&% and  kw.id_group = 27 and  fu.date_start > \'%&DATESTART&%\' and fu.date_start < \'%&DATEEND&%\' order by User',
		'where'=>'fund.idfu = fu.id_followup',
		'fund'=>', (select fu.id_followup as idfu from lcm_client as cl left join lcm_case_client_org as cco on cl.id_client = cco.id_client left join lcm_followup as fu on cco.id_case = fu.id_case left join lcm_keyword_client as kcl on kcl.id_client = cl.id_client left join lcm_keyword as k on k.id_keyword = kcl.id_keyword where k.id_group = 24 and k.id_keyword=%&FUNDER&% ) as fund'

		)
	);



















///	array 
///		(
///		'name'=>'clcount',
///		'type'=>'client',
///		'title'=>'Client count',
///		'select'=>'count(*) as Clients',	
///		'from'=>'lcm_client as cl'
///		),
///	array
///		(
///		'name'=>'ccount',
///		'type'=>'case',
///		'title'=>'Case count (by worker)',
///		'select'=>'count(*) as Cases',
///		'from'=>'lcm_case as c',
///		'join'=>'NATURAL JOIN lcm_case_author as ca',
////		'xtab-from'=>'lcm_author',
///		'xtab-sel'=>'id_author',
///		'xtab-title'=>'CONCAT(name_first," ",name_last)'
///		),
//	array//FU
//		(
//		'name'=>'worktype',
//		'title'=>'Work items (by worker)',
//		'type'=>'work',
//		'select'=>'k.title as "Work Type", count(*) as "Total"',
//		'from'=>'lcm_followup as fu',
//		'join'=>'LEFT JOIN lcm_keyword as k ON fu.type = k.name',
//		'group'=>'k.title',
//		'xtab-sel'=>'id_author',
//		'xtab-from'=>'lcm_author',
//		'xtab-title'=>'CONCAT(name_first," ",name_last)'
//		),
//	array//FU
//		(
//		'name'=>'worktypeii',
//		'type'=>'work',
//		'title'=>'Client contacts (by worker)',
//		'select'=>'k.title as "Work Type", count(*) as "Total"',
//		'from'=>'lcm_followup as fu',
//		'join'=>'LEFT JOIN lcm_keyword as k ON fu.type = k.name',
//		'group'=>'k.title',
//		'where'=>'k.description="clicon"',
//		'xtab-sel'=>'id_author',
//		'xtab-from'=>'lcm_author',
//		'xtab-title'=>'CONCAT(name_first," ",name_last)'
//		),
//	array//FU
//		(
//		'name'=>'outcomes',
//		'type'=>'work',
//		'title'=>'Outcomes and amounts',
//		'select'=>'k.title as "Outcome", count(*) as "Count", sum(fu.outcome_amount) as "Total Value £"',
//		'from'=>'lcm_keyword as k',
//		'join'=>'RIGHT JOIN lcm_followup as fu on k.id_keyword = fu.outcome',
//		'where'=>'k.id_group = 12',
//		'group'=>'k.title',
//		'union'=>'SELECT "Undefined", count(*), sum(outcome_amount) FROM lcm_followup WHERE outcome="0" UNION SELECT "Total", count(*), sum(outcome_amount) FROM lcm_followup WHERE ( (outcome != NULL) OR(outcome_amount != NULL)) '
//		),
//	array
//		(
//		'name'=>'ctype',
//		'type'=>'case',
//		'title'=>'Case count (by intervention)',
//		'select'=>'count(*) as Cases, SUM(IF((status="open" OR status="closed"),1,0)) as Casework, SUM(IF(status="draft",1,0)) as Oneoff',
//		'from'=>'lcm_case as c'
//		),
//
//	array
//		(
//		'name'=>'clgender',
//		'type'=>'client',
//		'title'=>'Client gender',
//		'select'=>'count(*) as Total, SUM(IF((gender="male"),1,0)) as Male, SUM(IF(gender="female",1,0)) as Female, SUM(IF(gender="unknown",1,0)) as Unknown',
//		'from'=>'lcm_client as cl',
//		),
//
//	array
//		(
//		'name'=>'cldisabled',
//		'title'=>'Client disability',
//		'select'=>'count(*) as Total',
//		'type'=>'client',
//		'from'=>'lcm_client as cl',
//		'join'=>'LEFT JOIN lcm_keyword_client as kcl ON cl.id_client = kcl.id_client LEFT JOIN lcm_keyword as k ON kcl.id_keyword = k.id_keyword',
//		'where'=>'k.id_group="23"',
//		'xtab-sel'=>'k.id_keyword',
//		'xtab-from'=>'lcm_keyword as k',
//		'xtab-title'=>'k.title',
//		'xtab-where'=>'k.id_group="23"'
//		),
//	array
//		(
//		'name'=>'clstatus',
//		'type'=>'client',
//		'title'=>'Client status',
//		'select'=>'k.title as title, count(kcl.id_client) as count',
//		'from'=>'lcm_keyword as k',
//		'join'=>'RIGHT JOIN lcm_keyword_client as kcl ON k.id_keyword= kcl.id_keyword LEFT JOIN lcm_client as cl ON kcl.id_client = cl.id_client',
//		'where'=>'k.id_group=19',
//		'group'=>'title',
//		'ttl'=>'yes'
//		),
//	array
//		(
//		'name'=>'cmatter',
//		'type'=>'case',
//		'title'=>'Case matter',
//		'select'=>'k.title as Matter, count(*) as Total',
//		'from'=>'lcm_keyword as k',
//		'join'=>'RIGHT JOIN lcm_keyword_case as kc ON k.id_keyword = kc.id_keyword RIGHT JOIN lcm_case as c ON kc.id_case = c.id_case',
//		'where'=>'k.id_group="27"',
//		'group'=>'k.title'
//		),
//	array
//		(
//		'name'=>'cmatterworker',
//		'type'=>'case',
//		'title'=>'Case matter (by worker)',
//		'select'=>'k.title as Matter, count(*) as Total',
//		'from'=>'lcm_keyword as k',
//		'join'=>'RIGHT JOIN lcm_keyword_case as kc ON k.id_keyword = kc.id_keyword RIGHT JOIN lcm_case as c ON kc.id_case = c.id_case LEFT JOIN lcm_case_author as ca ON c.id_case = ca.id_case',
//		'where'=>'k.id_group="27"',
//		'group'=>'k.title',
//		'xtab-from'=>'lcm_author',
//		'xtab-sel'=>'id_author',
//		'xtab-title'=>'CONCAT(name_first," ",name_last)'
//		),
//	array	
//		(
//		'name'=>'clnatsex',
//		'type'=>'client',
//		'title'=>'Client ethnicity (by gender)',
//		'select'=>'k.title as Ethnicity, count(*) as Total, SUM(IF((gender="male"),1,0)) as Male, SUM(IF(gender="female",1,0)) as Female, SUM(IF(gender="unknown",1,0)) as Unknown',
//		'from'=>'lcm_keyword as k',
//		'join'=>'RIGHT JOIN lcm_keyword_client as kcl ON k.id_keyword = kcl.id_keyword RIGHT JOIN lcm_client as cl ON kcl.id_client = cl.id_client',
//		'where'=>'k.id_group="20"',
//		'group'=>'k.title',
///		),
///	array	
///		(
///		'name'=>'clnatworker',
//		'type'=>'client',
//		'title'=>'Client ethnicity (by worker)',
//		'select'=>'k.title as Ethnicity, count(*) as Total',
//		'from'=>'lcm_keyword as k',
//		'join'=>'RIGHT JOIN lcm_keyword_client as kcl ON k.id_keyword = kcl.id_keyword RIGHT JOIN lcm_client as cl ON kcl.id_client = cl.id_client',
//		'where'=>'k.id_group="20"',
//		'group'=>'k.title',
//		'xtab-from'=>'lcm_author',
//		'xtab-sel'=>'id_author',
//		'xtab-title'=>'CONCAT(name_first," "name_last)'
//		)
//	);
?>
