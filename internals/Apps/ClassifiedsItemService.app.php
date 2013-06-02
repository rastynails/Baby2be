<?php

require_once DIR_APPS . 'appAux/ClassifiedsItemDao.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: ER
 * Date: Jan 13, 2009
 * 
 * Desc: Classifieds Item Service Class
 */
final class app_ClassifiedsItemService
{
    /**
     * @var ClassifiedsItemDao
     */
    private $classifiedsItemDao;

    /**
     * @var array
     */
    private $configs;

    /**
     * @var array
     */
    private static $classInstance;

    /**
     * Class constructor
     */
    private function __construct()
    {
        $this->classifiedsItemDao = new ClassifiedsItemDao();
        $this->configs = new SK_Config_Section('classifieds');
    }

    /**
     * Returns the only instance of the class
     *
     * @return app_ClassifiedsItemService
     */
    public static function newInstance()
    {
        if ( self::$classInstance === null )
            self::$classInstance = new self();
        return self::$classInstance;
    }

    /**
     * Returns Latest Items List
     *
     * @param string $entity
     * @return array
     */
    public function getLatestItems( $entity )
    {
        $count = $this->configs->items_count_on_page;
        $items = $this->classifiedsItemDao->findLatestItems($entity, $count);

        $file_service = app_ClassifiedsFileService::newInstance();

        $items_list = array();

        $itemIdList = array();
        foreach ( $items as $item )
        {
            if ( !empty($item) )
            {
                $itemIdList[] = $item->getId();
            }
        }

        $file_service->getItemsFileList($itemIdList);

        foreach ( $items as $item )
        {

            if ( !$item->getIs_approved() )
                continue;

            $item_info = array();

            $item_info['item_id'] = $item->getId();
            $item_info['profile_id'] = $item->getProfile_id();
            $item_info['title'] = app_TextService::stOutputFormatter($item->getTitle());
            $item_info['currency'] = $item->getCurrency();
            $item_info['budget_min'] = $item->getBudget_min();
            $item_info['budget_max'] = $item->getBudget_max();
            $item_info['price'] = $item->getPrice();
            $item_info['create_stamp'] = $item->getCreate_stamp();
            $item_info['end_stamp'] = $item->getEnd_stamp();

            //get item url
            $item_info['item_url'] = SK_Navigation::href('classifieds_item', array('item_id' => $item_info['item_id']));

            //get item thumb
            $item_files = $file_service->getItemFiles($item_info['item_id']);
            $item_info['item_thumb'] = ( $item_files[0] ) ? $item_files[0]['file_url'] : false;

            $items_list[] = $item_info;
        }

        return $items_list;
    }

    public function getSearchResultList( $keyword, $page = 1 )
    {
        $count = $this->configs->items_count_on_page;
        $first = ( intval($page) ) ? ($page - 1) * $count : 0;
        $item_list = array();

        $file_service = app_ClassifiedsFileService::newInstance();

        $items = $this->classifiedsItemDao->findItems($keyword, $first, $count);

        $itemIdList = array();
        foreach ( $items as $item )
        {
            $itemIdList[] = $item->getId();
        }

        $file_service->getItemsFileList($itemIdList);
        foreach ( $items as $item )
        {
            if ( !$item->getIs_approved() )
                continue;

            $item_info['item_id'] = $item->getId();
            $item_info['profile_id'] = $item->getProfile_id();
            $item_info['entity'] = $item->getEntity();
            $item_info['group_id'] = $item->getGroup_id();
            $item_info['title'] = app_TextService::stOutputFormatter($item->getTitle());
            $item_info['description'] = app_TextService::stOutputFormatter($item->getDescription());
            $item_info['currency'] = $item->getCurrency();
            $item_info['budget_min'] = $item->getBudget_min();
            $item_info['budget_max'] = $item->getBudget_max();
            $item_info['price'] = $item->getPrice();
            $item_info['create_stamp'] = $item->getCreate_stamp();
            $item_info['start_stamp'] = $item->getStart_stamp();
            $item_info['end_stamp'] = $item->getEnd_stamp();
            $item_info['last_bid'] = $this->classifiedsItemDao->findItemLastBid($item_info['item_id']);

            //get item url
            $item_info['item_url'] = SK_Navigation::href('classifieds_item', array('item_id' => $item_info['item_id']));

            //get item thumb
            $item_files = $file_service->getItemFiles($item_info['item_id']);
            $item_info['item_thumb'] = ( $item_files[0] ) ? $item_files[0]['file_url'] : false;

            $item_list[] = $item_info;
        }

        return $item_list;
    }

    /**
     * Returns Ending Soon Items List
     *
     * @param string $entity
     * @return array
     */
    public function getEndingSoonItems( $entity )
    {
        $count = $this->configs->items_count_on_page;
        $items = $this->classifiedsItemDao->findEndingSoonItems($entity, $count);

        $file_service = app_ClassifiedsFileService::newInstance();

        $items_list = array();
        $itemIdList = array();
        foreach ( $items as $item )
        {
            $itemIdList[] = $item->getId();
        }

        $file_service->getItemsFileList($itemIdList);
        foreach ( $items as $item )
        {
            $item_info = array();

            if ( !$item->getIs_approved() )
                continue;

            $item_info['item_id'] = $item->getId();
            $item_info['profile_id'] = $item->getProfile_id();
            $item_info['title'] = app_TextService::stOutputFormatter($item->getTitle());
            $item_info['currency'] = $item->getCurrency();
            $item_info['budget_min'] = $item->getBudget_min();
            $item_info['budget_max'] = $item->getBudget_max();
            $item_info['price'] = $item->getPrice();
            $item_info['create_stamp'] = $item->getCreate_stamp();
            $item_info['end_stamp'] = $item->getEnd_stamp();

            //get item url
            $item_info['item_url'] = SK_Navigation::href('classifieds_item', array('item_id' => $item_info['item_id']));

            //get item thumb
            $item_files = $file_service->getItemFiles($item_info['item_id']);
            $item_info['item_thumb'] = ( $item_files[0] ) ? $item_files[0]['file_url'] : false;

            $items_list[] = $item_info;
        }

        return $items_list;
    }

    /**
     * Returns new item's order
     * 	
     * @return int
     */
    public function getNewItemOrder()
    {
        return $this->classifiedsItemDao->findNewItemOrder();
    }

    /**
     * Saves item
     *
     * @param ClassifiedsItem $item
     */
    public function saveItem( ClassifiedsItem $item )
    {
        $this->classifiedsItemDao->saveOrUpdate($item);
    }

    /**
     * Returns items info
     *
     * @param int $item_id
     * @return array;
     */
    public function getItemInfo( $item_id )
    {
        if ( !$item_id )
        {
            return false;
        }

        $item = $this->classifiedsItemDao->findById($item_id);

        if ( !$item )
        {
            return false;
        }

        if ( !$item->getIs_approved() && !SK_HttpUser::isModerator() && ($item->getProfile_id() != SK_HttpUser::profile_id()) )
            return false;

        $item_info['item_id'] = $item->getId();
        $item_info['profile_id'] = $item->getProfile_id();
        $item_info['entity'] = $item->getEntity();
        $item_info['group_id'] = $item->getGroup_id();
        $item_info['title'] = app_TextService::stOutputFormatter($item->getTitle());
        $item_info['description'] = app_TextService::stOutputFormatter($item->getDescription());
        $item_info['currency'] = $item->getCurrency();
        $item_info['budget_min'] = $item->getBudget_min();
        $item_info['budget_max'] = $item->getBudget_max();
        $item_info['price'] = $item->getPrice();
        $item_info['create_stamp'] = $item->getCreate_stamp();
        $item_info['start_stamp'] = $item->getStart_stamp();
        $item_info['end_stamp'] = $item->getEnd_stamp();
        $item_info['edit_stamp'] = $item->getEdit_stamp();
        $item_info['edited_by_profile_id'] = $item->getEdited_by_profile_id();
        $item_info['order'] = $item->getOrder();
        $item_info['allow_bids'] = $item->getAllow_bids();
        $item_info['allow_comments'] = $item->getAllow_comments();
        $item_info['limited_bids'] = $item->getLimited_bids();
        $item_info['item_ended'] = ( $item_info['end_stamp'] && $item_info['end_stamp'] < time() ) ? true : false;
        $item_info['is_approved'] = $item->getIs_approved();
        $item_info['payment_dtls'] = $item->getPayment_dtls();

        return $item_info;
    }

    public function getItemNoPhoto()
    {
        return URL_LAYOUT_IMG . 'no_photo.jpg';
    }

    /**
     * Returns category items
     *
     * @param int $category_id
     * @param string $type
     * @param int $page
     * @return array
     */
    public function getCategoryItems( $category_id, $type, $page = 1 )
    {
        $count = $this->configs->items_count_on_page;
        $first = ( intval($page) ) ? ($page - 1) * $count : 0;
        $item_list = array();

        $file_service = app_ClassifiedsFileService::newInstance();

        if ( $type == 'latest' )
        {
            $items = $this->classifiedsItemDao->findCategoryLatestItems($category_id, $first, $count);
        }
        else
        {
            $items = $this->classifiedsItemDao->findCategoryEndingSoonItems($category_id, $first, $count);
        }

        $itemIdList = array();
        foreach ( $items as $item )
        {
            $itemIdList[] = $item->getId();
        }

        $file_service->getItemsFileList($itemIdList);

        foreach ( $items as $item )
        {
            if ( !$item->getIs_approved() )
                continue;

            $item_info['item_id'] = $item->getId();
            $item_info['profile_id'] = $item->getProfile_id();
            $item_info['entity'] = $item->getEntity();
            $item_info['group_id'] = $item->getGroup_id();
            $item_info['title'] = app_TextService::stOutputFormatter($item->getTitle());
            $item_info['description'] = app_TextService::stOutputFormatter($item->getDescription());
            $item_info['currency'] = $item->getCurrency();
            $item_info['budget_min'] = $item->getBudget_min();
            $item_info['budget_max'] = $item->getBudget_max();
            $item_info['price'] = $item->getPrice();
            $item_info['create_stamp'] = $item->getCreate_stamp();
            $item_info['start_stamp'] = $item->getStart_stamp();
            $item_info['end_stamp'] = $item->getEnd_stamp();

            //get item last bid
            $item_info['last_bid'] = $this->classifiedsItemDao->findItemLastBid($item_info['item_id']);

            //get item url
            $item_info['item_url'] = SK_Navigation::href('classifieds_item', array('item_id' => $item_info['item_id']));

            //get item thumb
            $item_files = $file_service->getItemFiles($item_info['item_id']);
            $item_info['item_thumb'] = ( $item_files[0] ) ? $item_files[0]['file_url'] : false;

            $item_list[] = $item_info;
        }

        return $item_list;
    }

    /**
     * Returns entity items list
     *
     * @param string $entity
     * @param string $type ('latest' | 'ending_soon')
     * @param int $page
     * @return array
     */
    public function getItemList( $entity, $type, $page = 1 )
    {
        $count = $this->configs->items_count_on_page;
        $first = ( intval($page) ) ? ($page - 1) * $count : 0;
        $item_list = array();

        $file_service = app_ClassifiedsFileService::newInstance();

        if ( $type == 'latest' )
        {
            $items = $this->classifiedsItemDao->findLatestItemList($entity, $first, $count);
        }
        else
        {
            $items = $this->classifiedsItemDao->findEndingSoonItemList($entity, $first, $count);
        }
        
        $itemIdList = array();
        foreach ( $items as $item )
        {
            $itemIdList[] = $item->getId();
        }

        $file_service->getItemsFileList($itemIdList);
        foreach ( $items as $item )
        {
            if ( !$item->getIs_approved() )
                continue;

            $item_info['item_id'] = $item->getId();
            $item_info['profile_id'] = $item->getProfile_id();
            $item_info['entity'] = $item->getEntity();
            $item_info['group_id'] = $item->getGroup_id();
            $item_info['title'] = app_TextService::stOutputFormatter($item->getTitle());
            $item_info['description'] = app_TextService::stOutputFormatter($item->getDescription());
            $item_info['currency'] = $item->getCurrency();
            $item_info['budget_min'] = $item->getBudget_min();
            $item_info['budget_max'] = $item->getBudget_max();
            $item_info['price'] = $item->getPrice();
            $item_info['create_stamp'] = $item->getCreate_stamp();
            $item_info['start_stamp'] = $item->getStart_stamp();
            $item_info['end_stamp'] = $item->getEnd_stamp();
            $item_info['last_bid'] = $this->classifiedsItemDao->findItemLastBid($item_info['item_id']);

            //get item url
            $item_info['item_url'] = SK_Navigation::href('classifieds_item', array('item_id' => $item_info['item_id']));

            //get item thumb
            $item_files = $file_service->getItemFiles($item_info['item_id']);
            $item_info['item_thumb'] = ( $item_files[0] ) ? $item_files[0]['file_url'] : false;

            $item_list[] = $item_info;
        }

        return $item_list;
    }

    public function getItemsToApproveCount()
    {
        return $this->classifiedsItemDao->findItemsToApproveCount();
    }

    public function findItemsToModerate( $page )
    {
        $first = ( $page - 1 ) * (int) $this->configs->items_count_on_page;
        $count = (int) $this->configs->items_count_on_page;

        return $this->classifiedsItemDao->findItemsToApprove($first, $count);
    }

    public function findItemsToModerateCount()
    {
        return $this->classifiedsItemDao->findItemsToApproveCount();
    }

    public function getIsAutoApprove()
    {
        return SK_Config::section('site.automode')->set_active_cls_on_creation;
    }

    /**
     * Returns category items count
     *
     * @param int $category_id
     * @return int
     */
    public function getCategoryItemsCount( $category_id, $type )
    {
        return $this->classifiedsItemDao->findCategoryItemsCount($category_id, $type);
    }

    /**
     * Returns items count
     *
     * @param string $entity
     * @return int
     */
    public function getItemsCount( $entity, $type )
    {
        return $this->classifiedsItemDao->findItemsCount($entity, $type);
    }

    public function getSearchResultCount( $keyword )
    {
        return $this->classifiedsItemDao->findSearchResultCount($keyword);
    }

    /**
     * Returns item's owner id
     *
     * @param int $item_id
     * @return int
     */
    public function getItemOwnerId( $item_id )
    {
        $item_info = $this->classifiedsItemDao->findById($item_id);

        if ( !$item_info )
        {
            return false;
        }

        return $item_info->getProfile_id();
    }

    /**
     * Deletes item
     *
     * @param int $item_id
     */
    public function deleteItem( $item_id )
    {
        //delete item
        $this->classifiedsItemDao->deleteById($item_id);
        //delete item files
        app_ClassifiedsFileService::stDeleteItemFiles($item_id);
        //delete item comments and bids
        app_CommentService::stDeleteClassifiedsComments($item_id);

        if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
        {
            app_Newsfeed::newInstance()->removeAction('post_classifieds_item', $item_id);
        }
    }

    public function approveItem( $item_id )
    {
        $this->classifiedsItemDao->approveById($item_id);
    }

    /**
     * Returns ClassifiedsItem object by  item_id
     *
     * @param integer $item_id
     * @return ClassifiedsItem
     */
    public function findById( $item_id )
    {
        return $this->classifiedsItemDao->findById($item_id);
    }

    /**
     * Returns cls available currencies
     *
     * @return array
     */
    public function getCurrencies()
    {
        $currencies = array();
        $cur_arr = array();

        $currencies = $this->configs->getConfigValues('currency');

        foreach ( $currencies as $currency )
        {
            $cur_arr[$currency['label']] = $currency['value'];
        }

        return $cur_arr;
    }
    /** ---------------------------- static interface -------------------------------------- * */

    /**
     * Returns Latest Items List
     *
     * @param string $entity
     * @return array
     */
    public static function stGetLatestItems( $entity )
    {
        $service = self::newInstance();

        return $service->getLatestItems($entity);
    }

    public static function stGetSearchResultItems( $keyword, $page = 1 )
    {
        $service = self::newInstance();

        return $service->getSearchResultList($keyword, $page);
    }

    /**
     * Returns Ending Soon Items List
     *
     * @param string $entity
     * @return array
     */
    public static function stGetEndingSoonItems( $entity )
    {
        $service = self::newInstance();

        return $service->getEndingSoonItems($entity);
    }

    /**
     * Returns new item's order
     * 	
     * @return int
     */
    public static function stGetNewItemOrder()
    {
        $service = self::newInstance();

        return $service->getNewItemOrder();
    }

    /**
     * Returns items info
     *
     * @param int $item_id
     * @return array;
     */
    public static function stGetItemInfo( $item_id )
    {
        $service = self::newInstance();

        return $service->getItemInfo($item_id);
    }

    /**
     * Returns category items
     *
     * @param int $category_id
     * @param string $type
     * @param int $page
     * @return array
     */
    public static function stGetCategoryItems( $category_id, $type, $page )
    {
        $service = self::newInstance();

        return $service->getCategoryItems($category_id, $type, $page);
    }

    /**
     * Returns entity items list
     *
     * @param string $entity
     * @param string $type ('latest' | 'ending_soon')
     * @param int $page
     * @return array
     */
    public static function stGetItemList( $entity, $type, $page )
    {
        $service = self::newInstance();

        return $service->getItemList($entity, $type, $page);
    }

    /**
     * Returns entity list of items to approve 
     *
     * @param string $entity
     * @return array
     */
    public static function stGetItemsToApprove()
    {
        $service = self::newInstance();

        return $service->getItemsToApprove();
    }

    /**
     * Returns number of items to approve 
     *
     * @return array
     */
    public static function stGetItemsToApproveCount()
    {
        $service = self::newInstance();

        return $service->getItemsToApproveCount();
    }

    /**
     * Returns category items count
     *
     * @param int $category_id
     * @return int
     */
    public static function stGetCategoryItemsCount( $category_id, $type = 'latest' )
    {
        $service = self::newInstance();

        return $service->getCategoryItemsCount($category_id, $type);
    }

    /**
     * Returns items count
     *
     * @param string $entity
     * @return int
     */
    public static function stGetItemsCount( $entity, $type = 'latest' )
    {
        $service = self::newInstance();

        return $service->getItemsCount($entity, $type);
    }

    public static function stGetSearchResultItemsCount( $keyword )
    {
        $service = self::newInstance();

        return $service->getSearchResultCount($keyword);
    }

    /**
     * Returns item's owner id
     *
     * @param int $item_id
     * @return int
     */
    public static function stGetItemOwnerId( $item_id )
    {
        $service = self::newInstance();

        return $service->getItemOwnerId($item_id);
    }

    /**
     * Returns item's id
     *
     * @param int $item_id
     * @return int
     */
    public static function stDeleteItem( $item_id )
    {
        $service = self::newInstance();

        return $service->deleteItem($item_id);
    }

    /**
     * Approve's item
     *
     * @param int $item_id
     * @return int
     */
    public static function stApproveItem( $item_id )
    {
        return SK_MySQL::query(SK_MySQL::placeholder("UPDATE `" . TBL_CLASSIFIEDS_ITEM . "` SET `is_approved`=? WHERE `id`='?'", 1, $item_id));
    }

    /**
     * Returns cls available currencies
     *
     * @return array
     */
    public static function stGetCurrencies()
    {
        $service = self::newInstance();

        return $service->getCurrencies();
    }

    public static function findAllActiveItems()
    {
        $service = self::newInstance();
        return $service->classifiedsItemDao->findAll();
    }
    
    public static function stFilterPaymentDetails( $text )
    {
        $tags = array('form', 'a', 'input');
        $pair_tags = array('form');
        $attrs = array('name', 'href', 'width', 'height', 'align', 'title', 'type', 'value', 'src', 'target', 'method', 'action');
        
        $addTextFilter = new InputFilter($tags, $attrs);
                
        $return_text = $addTextFilter->process($text);

        foreach ( $pair_tags as $value )
        {
            $count = substr_count($return_text, "<".$value.">");
            $count2 = substr_count($return_text, "</".$value.">");

            if ( $count === 0 ) continue;

            $return_text .= str_repeat('</'.$value.'>', ($count2-$count));
        }

        return $return_text;
    }
}