<?php

namespace Baiy\Cadmin;

use Baiy\Cadmin\Model\AdminGroup;
use Baiy\Cadmin\Model\AdminMenu;
use Baiy\Cadmin\Model\AdminMenuGroup;
use Baiy\Cadmin\Model\AdminRequest;
use Baiy\Cadmin\Model\AdminRequestGroup;
//use Baiy\Cadmin\Model\AdminToken;
use Baiy\Cadmin\Model\AdminUser;
use Baiy\Cadmin\Model\AdminUserGroup;

$superGroupId = 1;

$menus = [
    ['id' => 1, 'parent_id' => 0, 'name' => '系统设置', 'url' => '', 'icon' => 'md-settings', 'description' => '', 'sort' => 0],
    ['id' => 2, 'parent_id' => 1, 'name' => '管理员设置', 'url' => '/system/user', 'icon' => 'ios-people', 'description' => '', 'sort' => 0],
    ['id' => 3, 'parent_id' => 1, 'name' => '请求管理', 'url' => '/system/request', 'icon' => 'ios-list', 'description' => '', 'sort' => 1],
    ['id' => 4, 'parent_id' => 1, 'name' => '权限管理', 'url' => '/system/authorize', 'icon' => 'ios-lock-outline', 'description' => '', 'sort' => 2],
    ['id' => 5, 'parent_id' => 1, 'name' => '菜单管理', 'url' => '/system/menu', 'icon' => 'md-menu', 'description' => '', 'sort' => 3],
];

$requests = [
    ['id' => 1, 'type' => 1, 'name' => '登录', 'action' => '/login', 'call' => 'System\\Index::login'],
    ['id' => 2, 'type' => 1, 'name' => '退出', 'action' => '/logout', 'call' => 'System\\Index::logout'],
    ['id' => 3, 'type' => 1, 'name' => '初始数据加载', 'action' => '/load', 'call' => 'System\\Index::load'],
    ['id' => 4, 'type' => 1, 'name' => '菜单管理-列表数据', 'action' => '/system/menu/lists', 'call' => 'System\\Menu::lists'],
    ['id' => 5, 'type' => 1, 'name' => '管理员设置-列表数据', 'action' => '/system/user/lists', 'call' => 'System\\User::lists'],
    ['id' => 6, 'type' => 1, 'name' => '请求管理-请求保存', 'action' => '/system/request/save', 'call' => 'System\\Request::save'],
    ['id' => 7, 'type' => 1, 'name' => '请求管理-请求删除', 'action' => '/system/request/remove', 'call' => 'System\\Request::remove'],
    ['id' => 8, 'type' => 1, 'name' => '请求管理-列表数据', 'action' => '/system/request/lists', 'call' => 'System\\Request::lists'],
    ['id' => 9, 'type' => 1, 'name' => '管理员设置-用户保存', 'action' => '/system/user/save', 'call' => 'System\\User::save'],
    ['id' => 10, 'type' => 1, 'name' => '管理员设置-用户删除', 'action' => '/system/user/remove', 'call' => 'System\\User::remove'],
    ['id' => 11, 'type' => 1, 'name' => '菜单管理-排序', 'action' => '/system/menu/sort', 'call' => 'System\\Menu::sort'],
    ['id' => 12, 'type' => 1, 'name' => '菜单管理-菜单保存', 'action' => '/system/menu/save', 'call' => 'System\\Menu::save'],
    ['id' => 13, 'type' => 1, 'name' => '菜单管理-菜单删除', 'action' => '/system/menu/remove', 'call' => 'System\\Menu::remove'],
    ['id' => 14, 'type' => 1, 'name' => '权限管理-列表数据', 'action' => '/system/authorize/lists', 'call' => 'System\\Authorize::lists'],
    ['id' => 15, 'type' => 1, 'name' => '权限管理-分组保存', 'action' => '/system/authorize/save', 'call' => 'System\\Authorize::save'],
    ['id' => 16, 'type' => 1, 'name' => '权限管理-分组删除', 'action' => '/system/authorize/remove', 'call' => 'System\\Authorize::remove'],
    ['id' => 17, 'type' => 1, 'name' => '权限管理-获取未分配请求', 'action' => '/system/authorize/getRequestAssign', 'call' => 'System\\Authorize::getRequestAssign'],
    ['id' => 18, 'type' => 1, 'name' => '权限管理-分配请求', 'action' => '/system/authorize/assignRequest', 'call' => 'System\\Authorize::assignRequest'],
    ['id' => 19, 'type' => 1, 'name' => '权限管理-移除分配请求', 'action' => '/system/authorize/removeRequest', 'call' => 'System\\Authorize::removeRequest'],
    ['id' => 20, 'type' => 1, 'name' => '权限管理-获取用户分配', 'action' => '/system/authorize/getUserAssign', 'call' => 'System\\Authorize::getUserAssign'],
    ['id' => 21, 'type' => 1, 'name' => '权限管理-分配用户', 'action' => '/system/authorize/assignUser', 'call' => 'System\\Authorize::assignUser'],
    ['id' => 22, 'type' => 1, 'name' => '权限管理-获取菜单分配', 'action' => '/system/authorize/getMenuAssign', 'call' => 'System\\Authorize::getMenuAssign'],
    ['id' => 23, 'type' => 1, 'name' => '权限管理-分配菜单', 'action' => '/system/authorize/assignMenu', 'call' => 'System\\Authorize::assignMenu'],
];
// 无分组请求
$noneGroupRequest = [1, 2, 3];

return [
    AdminGroup::table()        => [
        'id'   => $superGroupId,
        'name' => '系统管理员'
    ],
    AdminUser::table()         => [
        'id'       => 1,
        'username' => 'admin',
        'password' => Helper::createPassword('123456')
    ],
    AdminUserGroup::table()    => [
        'admin_group_id' => $superGroupId,
        'admin_user_id'  => 1
    ],
    AdminMenu::table()         => $menus,
    AdminMenuGroup::table()    => array_map(function ($id) use ($superGroupId) {
        return [
            'admin_group_id' => $superGroupId,
            'admin_menu_id'  => $id
        ];
    }, array_column($menus, 'id')),
    AdminRequest::table()      => array_map(function ($item) {
        $item['call'] = '\\'.trim(__NAMESPACE__, '\\').'\\'.$item['call'];
        return $item;
    }, $requests),
    AdminRequestGroup::table() => array_values(array_map(
        function ($id) use ($superGroupId) {
            return [
                'admin_group_id'   => $superGroupId,
                'admin_request_id' => $id
            ];
        },
        array_filter(array_column($requests, 'id'), function ($id) use ($noneGroupRequest) {
            return !in_array($id, $noneGroupRequest);
        })
    )),
];