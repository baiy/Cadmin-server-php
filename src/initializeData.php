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
    ['id' => 3, 'parent_id' => 1, 'name' => '权限管理', 'url' => '', 'icon' => 'ios-list', 'description' => '', 'sort' => 1],
    ['id' => 4, 'parent_id' => 3, 'name' => '权限组', 'url' => '/system/group', 'icon' => 'ios-lock-outline', 'description' => '', 'sort' => 0],
    ['id' => 5, 'parent_id' => 3, 'name' => '请求管理', 'url' => '/system/request', 'icon' => 'ios-list', 'description' => '', 'sort' => 1],
    ['id' => 6, 'parent_id' => 3, 'name' => '菜单管理', 'url' => '/system/menu', 'icon' => 'md-menu', 'description' => '', 'sort' => 2],
];

$requests = [
    ['id' => 1, 'type' => 1, 'name' => '登录', 'action' => '/login', 'call' => 'System\\Index::login'],
    ['id' => 2, 'type' => 1, 'name' => '退出', 'action' => '/logout', 'call' => 'System\\Index::logout'],
    ['id' => 3, 'type' => 1, 'name' => '初始数据加载', 'action' => '/load', 'call' => 'System\\Index::load'],
    ['id' => 4, 'type' => 1, 'name' => '通用文件上传', 'action' => '/upload', 'call' => 'System\\Index::upload'],
    ['id' => 100, 'type' => 1, 'name' => '菜单管理-列表数据', 'action' => '/system/menu/lists', 'call' => 'System\\Menu::lists'],
    ['id' => 101, 'type' => 1, 'name' => '菜单管理-排序', 'action' => '/system/menu/sort', 'call' => 'System\\Menu::sort'],
    ['id' => 102, 'type' => 1, 'name' => '菜单管理-菜单保存', 'action' => '/system/menu/save', 'call' => 'System\\Menu::save'],
    ['id' => 103, 'type' => 1, 'name' => '菜单管理-菜单删除', 'action' => '/system/menu/remove', 'call' => 'System\\Menu::remove'],
    ['id' => 104, 'type' => 1, 'name' => '请求管理-请求保存', 'action' => '/system/request/save', 'call' => 'System\\Request::save'],
    ['id' => 105, 'type' => 1, 'name' => '请求管理-请求删除', 'action' => '/system/request/remove', 'call' => 'System\\Request::remove'],
    ['id' => 106, 'type' => 1, 'name' => '请求管理-列表数据', 'action' => '/system/request/lists', 'call' => 'System\\Request::lists'],
    ['id' => 107, 'type' => 1, 'name' => '管理员设置-列表数据', 'action' => '/system/user/lists', 'call' => 'System\\User::lists'],
    ['id' => 108, 'type' => 1, 'name' => '管理员设置-用户保存', 'action' => '/system/user/save', 'call' => 'System\\User::save'],
    ['id' => 109, 'type' => 1, 'name' => '管理员设置-用户删除', 'action' => '/system/user/remove', 'call' => 'System\\User::remove'],
    ['id' => 110, 'type' => 1, 'name' => '管理员设置-获取用户分组', 'action' => '/system/auth/getGroupByUser', 'call' => 'System\\Auth::getGroupByUser'],
    ['id' => 111, 'type' => 1, 'name' => '管理员设置-关联分组', 'action' => '/system/auth/relateGroupToUser', 'call' => 'System\\Auth::relateGroupToUser'],
    ['id' => 112, 'type' => 1, 'name' => '权限管理-列表数据', 'action' => '/system/group/lists', 'call' => 'System\\Group::lists'],
    ['id' => 113, 'type' => 1, 'name' => '权限管理-分组保存', 'action' => '/system/group/save', 'call' => 'System\\Group::save'],
    ['id' => 114, 'type' => 1, 'name' => '权限管理-分组删除', 'action' => '/system/group/remove', 'call' => 'System\\Group::remove'],
    ['id' => 115, 'type' => 1, 'name' => '权限管理-获取分配请求数据', 'action' => '/system/auth/getRequest', 'call' => 'System\\Auth::getRequest'],
    ['id' => 116, 'type' => 1, 'name' => '权限管理-分配请求', 'action' => '/system/auth/assignRequest', 'call' => 'System\\Auth::assignRequest'],
    ['id' => 117, 'type' => 1, 'name' => '权限管理-移除分配请求', 'action' => '/system/auth/removeRequest', 'call' => 'System\\Auth::removeRequest'],
    ['id' => 118, 'type' => 1, 'name' => '权限管理-获取菜单分配数据', 'action' => '/system/auth/getMenu', 'call' => 'System\\Auth::getMenu'],
    ['id' => 119, 'type' => 1, 'name' => '权限管理-分配菜单', 'action' => '/system/auth/assignMenu', 'call' => 'System\\Auth::assignMenu'],
    ['id' => 120, 'type' => 1, 'name' => '权限管理-获取关联用户数据', 'action' => '/system/auth/getUserByGroup', 'call' => 'System\\Auth::getUserByGroup'],
];

// 无分组请求
$noneGroupRequest = [1, 2, 3, 4];

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