<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */
use Symfony\Component\Finder\Finder;

return [
    'enable' => true,
    'port' => 9504,
    'json_dir' => BASE_PATH . '/storage/swagger',
    'html' => BASE_PATH . '/storage/swagger/index.html',
    'url' => '/swagger',
    'auto_generate' => true,
    'scan' => [
        'paths' => Finder::create()
            ->in([BASE_PATH . '/plugin',BASE_PATH . '/app/Http', BASE_PATH . '/app/Schema',])
            // ->in([BASE_PATH . '/plugin'])
            ->name('*.php')
            ->getIterator(),
    ],
    'processors' => [],
];
