<?php 

namespace App\Http\Controllers\WebSocket\Traits;

/**
 *  Списки для рассылки:
 */
trait ClientsTrait
{
    
    /**
     *  Массив пользователей:
     *  $userId => [$socketId, $socketId]
     */
    private $usersSockets = [];
    
    /**
     *  Массив сокетов:
     *  [$socketId => $userId]
     */
    private $socketIds = [];
    
    /**
     *  Массив сокетов, которые подписались на обновления:
     *  [$socketId]
     */
    private $subscribedToUpdatesSocketIds = [];
    
    /**
     *  Сокеты с правами админа:
     *  [$socketId]
     */
    private $adminSocketIds = [];
    
    /**
     *  Сохранить подключение:
     */
    public function rememberSocketId($userId, $socketId)
    {
        if (!array_key_exists($socketId, $this->socketIds)) {
            
            $this->socketIds[$socketId] = $userId;
            $this->usersSockets[$userId][] = $socketId;
            
        }
    }
    
    /**
     *  Сохранить подключение админа:
     */
    public function rememberAdminSocketId($socketId)
    {
        if (!in_array($socketId, $this->adminSocketIds)) {
            
            $this->adminSocketIds[] = $socketId;
        }
    }
    
    /**
     *  Является ли подключение админским:
     */
    public function isAdminSocketId($socketId)
    {
        if (in_array($socketId, $this->adminSocketIds)) {
            
            return true;
        }
        
        return false;
    }
    
    /**
     *  Удалить подключение:
     */
    public function forgetSocketId($socketId)
    {
        if (array_key_exists($socketId, $this->socketIds)) {
            
            $userId = $this->socketIds[$socketId];
            unset($this->socketIds[$socketId]);
            
            foreach($this->usersSockets[$userId] as $key => $savedSocketId) {
                if($savedSocketId == $socketId) {
                    unset($this->usersSockets[$userId][$key]);
                }
            }
        }
        
        // Удалить админское соединение:
        if(in_array($socketId, $this->adminSocketIds)) {
            foreach($this->adminSocketIds as $key => $savedSocketId) {
                if($savedSocketId == $socketId) {
                    unset($this->adminSocketIds[$key]);
                }
            }
        }
    }
    
    /**
     *  Найти пользователя по идентификатору подключения:
     */
    public function getUserBySocketId($socketId)
    {
        if (array_key_exists($socketId, $this->socketIds)) {
            return $this->socketIds[$socketId];
        }
        
        return null;
    }
    
    /**
     *  Найти все подключения пользователя:
     */
    public function getUserSockets($socketId)
    {
        $array = [];
        $savedSockets = $this->usersSockets[$this->socketIds[$socketId]];

        foreach($savedSockets as $savedSocket) {
            $array[] = $savedSocket;
        }
        
        return $array;
    }
    
    /**
     *  Найти все подключения пользователя по его ID:
     */
    public function getSocketsByUserId($userId)
    {
        if(isset($this->usersSockets[$userId])) {
            return $this->usersSockets[$userId];
        }
        
        return [];
    }
    
    /**
     *  Найти остальные подключения пользователя:
     */
    public function getOtherUserSockets($socketId)
    {
        $array = [];
        $savedSockets = $this->usersSockets[$this->socketIds[$socketId]];
        
        foreach($savedSockets as $key => $savedSocket) {
            if($socketId != $savedSocket) {
                $array[] = $savedSocket;
            }
        }
        
        return $array;
    }
    
    /**
     *  Подписать на обновления:
     */
    public function subsctibeSocketToUpdates($socketId)
    {
        if (!in_array($socketId, $this->subscribedToUpdatesSocketIds)) {
            $this->subscribedToUpdatesSocketIds[] = $socketId;
            return true;
        }
        
        return false;
    }
    
    /**
     *  Отписать от обновлений:
     */
    public function unSubsctibeSocketToUpdates($socketId)
    {
        foreach($this->subscribedToUpdatesSocketIds as $key => $subscribedSocketId) {
            if($subscribedSocketId == $socketId) {
                unset($this->subscribedToUpdatesSocketIds[$key]);
                return true;
            }
        }
        
        return false;
    }
    
    /**
     *  Проверить подписан ли фронтенд на обновления:
     */
    public function isSubsctibedToUpdates($socketId)
    {
        if (in_array($socketId, $this->subscribedToUpdatesSocketIds)) {
            return true;
        }
        
        return false;
    }
    
    /**
     *  Информация о всех, кто онлайн:
     */
    public function getOnlineStatistics()
    {
        $array = [];
        
        foreach($this->usersSockets as $userId => $userSockets) {
            if(count($userSockets) > 0) {
                $array[$userId] = $userSockets;
            }
        }
        
        return [
            "user_sessions" => $array,
            "admin_sessions" => $this->adminSocketIds,
        ];
    }
    
}