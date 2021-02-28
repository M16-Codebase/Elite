<?php
/**
 * Created by PhpStorm.
 * User: pahus
 * Date: 16.11.2017
 * Time: 10:59
 */


namespace Modules\Ratings;

use Models\CatalogManagement\Item as ItemEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Models\CatalogManagement\Properties\Factory AS PropertyFactory;
use App\Configs\CatalogConfig;
use Models\CatalogManagement\CatalogHelpers\Item\RatingHelper;

class Ratings extends \LPS\WebModule
{

    const RATING_PROP = CatalogConfig::RATING_PROP;
    const MARKS_PROP = CatalogConfig::MARKS_PROP;

    public function index()
    {
        // TODO: Implement index() method.
    }

    public function set()
    {
        $index = $this->request->get('index');
        $objId = $this->request->get('objId');

        $errors = array();

        if (is_null($objId) || $objId === "") {
            $errors[] = 'Object ID is NULL';
        }

        $item = ItemEntity::getById($objId);
        $type = $item->getType();
        $item_properties = PropertyFactory::search($type['id'],
            PropertyFactory::P_NOT_VIEW | PropertyFactory::P_NOT_DEFAULT | PropertyFactory::P_NOT_RANGE | PropertyFactory::P_ITEMS,
            'key', 'group', 'parents', array());


        $marks = RatingHelper::getAsArray($item['marks']);

        $marks[] = $index;
        $marks = RatingHelper::asString($marks);
        $item->updateValueByKey(self::MARKS_PROP, $marks);

        $rating = RatingHelper::calculateRating($item);
        $item->updateValueByKey(self::RATING_PROP, $rating);
        RatingHelper::setRatingIP($objId, $index);

        //dump($x);

        if (count($errors)) {
            return  new JsonResponse(json_encode(array('errors' => $errors)));
        }

        $json = array('data' => $rating);
        $response = new JsonResponse($json);
        $response->headers->setCookie(new Cookie(CatalogConfig::RATING_COOKIE_NAME . $objId, $rating));
        return $response;
    }

}
