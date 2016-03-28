<?php 
$dir=$argv[1];
$modelDir="$dir/models";
if (!is_dir($modelDir)) {
	echo "Directory $modelDir doesn't exist";
	exit;
}

$steps=explode('/',trim($dir,'/'));
// print_r($steps);
$domain=$steps[count($steps)-1];

$domainDir="../domains/$domain";
if (!is_dir($domainDir)) {
	mkdir($domainDir,0755);
}

$modelsDir="$domainDir/Models";
if (!is_dir($modelsDir)) {
	mkdir($modelsDir,0755);
}

$schemasDir="$domainDir/Schemas";
if (!is_dir($schemasDir)) {
	mkdir($schemasDir,0755);
}

$listDomainTypesFile="$domainDir/ListDomainTypes.php";

// class Берлога extends Model {
//     public static $PrimaryKeyName='primarykey';
//     public static $AttrTypes=[
//         'Наименование'=>'string',
//         'Комфортность'=>'integer',
//         'Заброшена'=>'boolean',
//         ];
// 
//     public static $relationshipList=[
//         'ЛесРасположения'=>'Лес',
//         'Медведь'=>'Медведь'
//         ];
// }

$fd=dir($modelDir);
$listModels=[];
while ($entry=$fd->read()) {
	if (substr($entry,-5)!='.json') continue;
	$file="$modelDir/$entry";
	$modelName=substr($entry,0,-5);
	$phoModelName=str_replace('-','_',$modelName);
	$listModels[]="'$phoModelName'";
	$classFile="$modelsDir/${phoModelName}.php";
	$schemasFile="$schemasDir/SchemaOf${phoModelName}.php";
	$fp=fopen($file,'r');
	echo "domain=$domain modelName=$modelName\n";
	$str=fread($fp,filesize($file));
	$pos=strpos($str,'{');
	if ($pos>0) {
		$str=substr($str,$pos);
	}
// 	echo $str;
	$desc=json_decode($str,true);
	$phpClassCode="<?php\nuse fja\Model;\nclass $phoModelName extends Model {\n";
	$phpClassCode.="\tpublic static \$PrimaryKeyName='primaryKey';\n";
	$attrs=[];
	foreach ($desc['attrs'] as $attr) {
		$name=$attr['name'];
		$type=$attr['type'];
		$attrs[]="'$name'=>'$type'";
	}
	$phpClassCode.="\tpublic static \$AttrTypes=[\n\t\t" . implode(",\n\t\t",$attrs) . "\n\t];\n";

	$attrs=[];
// 	print_r($desc['belongsTo']);
	foreach ($desc['belongsTo'] as $relationship) {
		$name=$relationship['name'];
		$relationshipName=$relationship['relatedTo'];
		$attrs[]="'$name'=>'$relationshipName'";
	}
	$phpClassCode.="\tpublic static \$relationshipList=[\n\t\t" . implode(",\n\t\t",$attrs) . "\n\t];\n";
	
	$attrs=[];
// 	print_r($desc);
	foreach ($desc['hasMany'] as $relationship) {
		$name=$relationship['name'];
		$relationshipName=$relationship['relatedTo'];
		$attrs[]="'$name'=>'${relationshipName}[]'";
	}
	$phpClassCode.="\tpublic static \$reverseRelationshipsList=[\n\t\t" . implode(",\n\t\t",$attrs) . "\n\t];\n";
	$phpClassCode.="}\n";
	
	$fpClass=fopen($classFile,'w');
	fwrite($fpClass,$phpClassCode);
	fclose($fpClass);
	
// 	echo "$phpClassCode";
	

	$phpSchemasCode="<?php\nuse fja\Schema;class SchemaOf$phoModelName extends Schema {\n";
	$phpSchemasCode.="\tpublic static \$IsShowSelfInIncluded=true;\n";
	$phpSchemasCode.="\tpublic static \$ResourceType='$phoModelName';\n";
	$phpSchemasCode.="\tpublic static \$SelfSubUrl='/${phoModelName}s/';\n";
	$phpSchemasCode.="}\n";
	
	$fpSchemas=fopen($schemasFile,'w');
	fwrite($fpSchemas,$phpSchemasCode);
	fclose($fpSchemas);	
}

$phpListDomainTypes="<?php\nuse \\fja\\ListTypes;\nclass ListDomainTypes  extends ListTypes {\n\t public static \$listTypes= [\n\t" .
	implode(",\n\t\t",$listModels) . 
	"\n\t];\n}\n";
$fpListDomainTypes=fopen($listDomainTypesFile,'w');
fwrite($fpListDomainTypes,$phpListDomainTypes);
fclose($fpListDomainTypes);	