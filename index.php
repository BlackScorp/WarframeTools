<?php
declare(strict_types=1);
error_reporting(-1);
ini_set('display_errors', '1');
set_time_limit(0);

$languages = json_decode(file_get_contents(__DIR__ . '/languages.json'), true);
$modList = json_decode(file_get_contents(__DIR__ . '/mod_list.json'), true);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Warframe mods</title>
    <style>
        tr:hover{
            background: #aaaaaa;
        }
    </style>
</head>
<body>
<h1>Warframe Modnames in different languages</h1>
<p>Loaded modnames from https://warframe.fandom.com/wiki/Mods</p>
<?php foreach ($modList as $category => $languageData): ?>
    <h3><?= explode('-', $category)[0] ?></h3>
    <table border="1">
        <thead>
        <tr>
            <?php foreach ($languages as $languageKey => $languageTitle): ?>
                <th><?= $languageTitle ?></th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>

        <?php foreach ($languageData as $modName) : ?>
            <tr>
                <?php foreach ($languages as $languageKey  => $languageTitle): ?>
                    <td><?= isset($modName[$languageKey])?$modName[$languageKey]:'&nbsp;'?></td>
                <?php endforeach ?>

            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endforeach; ?>

</body>
</html>
