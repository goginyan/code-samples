<?php

namespace App\Components\Redemption\HotelBeds;

use App\Components\Redemption\HotelBeds\Api\HotelBedsApi;
use App\Components\Redemption\HotelBeds\Config\HotelBedsConfigManager;
use App\Components\Redemption\HotelBeds\ImportStatic\HotelBedsDictionaryResponse;
use App\Components\Redemption\HotelBeds\VO\HotelBedsSearchCode;
use App\Repositories\HotelBeds\HotelBedsRepository;
use App\Services\Auth\UserContext;

class HotelBedsAdapter
{
    private $api;
    private $exceptionHandler;
    private $configManager;
    private $hotelFactory;
    private $repository;

    /**
     * HotelBedsService constructor.
     * @param HotelBedsExceptionHandler $hotelBedsExceptionHandler
     * @param HotelBedsConfigManager $configManager
     * @param HotelBedsHotelFactory $hotelFactory
     * @param HotelBedsRepository $repository
     */
    public function __construct(HotelBedsExceptionHandler $hotelBedsExceptionHandler,
                                HotelBedsConfigManager $configManager,
                                HotelBedsHotelFactory $hotelFactory,
                                HotelBedsRepository $repository)
    {
        $this->configManager = $configManager;
        $this->api = $this->api();
        $this->exceptionHandler = $hotelBedsExceptionHandler;
        $this->hotelFactory = $hotelFactory;
        $this->repository = $repository;
    }

    /**
     * @return HotelBedsApi|mixed
     */
    protected function api()
    {
        return $this->api ?: $this->api = new HotelBedsApi($this->configManager->getConfig());
    }

    /**
     * @param $filters
     * @return mixed
     * @throws \App\Exceptions\HotelBeadsException
     */
    public function getCountries($filters)
    {
        $apiData = $this->exceptionHandler->handle(function () use ($filters) {
            return $this->api->getCountries($filters);
        });
        $result = [];

        if (!empty($apiData['countries'])) {
            foreach ($apiData['countries'] as $data) {
                $result[] = $this->hotelFactory->createHotelBedsStaticsCountry($data);
            }
        }
        return new HotelBedsDictionaryResponse(
            collect($result),
            $apiData['total']
        );
    }

    /**
     * @param $filters
     * @return HotelBedsDictionaryResponse
     * @throws \App\Exceptions\HotelBeadsException
     */
    public function getCategories($filters)
    {
        $apiData = $this->exceptionHandler->handle(function () use ($filters) {
            return $this->api->getCategories($filters);
        });
        $result = [];
        if (!empty($apiData['categories'])) {
            foreach ($apiData['categories'] as $data) {
                $result[] = $this->hotelFactory->createHotelBedsStaticsCategory($data);
            }
        }

        return new HotelBedsDictionaryResponse(
            collect($result),
            $apiData['total']
        );
    }

    /**
     * @param $filters
     * @return mixed
     * @throws \App\Exceptions\HotelBeadsException
     */
    public function getDestinations($filters)
    {
        $apiData = $this->exceptionHandler->handle(function () use ($filters) {
            return $this->api->getDestinations($filters);
        });
        $result = [];

        if (!empty($apiData['destinations'])) {
            foreach ($apiData['destinations'] as $data) {
                $result[] = $this->hotelFactory->createHotelBedsStaticsDestination($data);
            }
        }
        return new HotelBedsDictionaryResponse(
            collect($result),
            $apiData['total']
        );
    }

    /**
     * @param $filters
     * @return array
     * @throws \App\Exceptions\HotelBeadsException
     */
    public function getCurrencies($filters)
    {
        return $this->exceptionHandler->handle(function () use ($filters) {
            return $this->api->getCurrencies($filters);
        });
    }

    /**
     * @param $filters
     * @return mixed
     * @throws \App\Exceptions\HotelBeadsException
     */
    public function getHotels($filters)
    {

        $apiData = $this->exceptionHandler->handle(function () use ($filters) {
            return $this->api->getHotels($filters);
        });
        $result = [];

        if (!empty($apiData['hotels'])) {
            foreach ($apiData['hotels'] as $data) {
                $result[] = $this->hotelFactory->createHotelBedsStaticsHotel($data);
            }
        }
        return new HotelBedsDictionaryResponse(
            collect($result),
            $apiData['total']
        );
    }

    /**
     * @param $hotelCode
     * @param $filters
     * @return array
     * @throws \App\Exceptions\HotelBeadsException
     */
    public function getHotelDetails($hotelCode, $filters)
    {
        return $this->exceptionHandler->handle(function () use ($hotelCode, $filters) {
            return $this->api->getHotelDetails($hotelCode, $filters);
        });
    }

    public function getAccommodations($filters)
    {
        return $this->exceptionHandler->handle(function () use ($filters) {
            return $this->api->getAccommodations($filters);
        });
    }

    /**
     * @param $filters
     * @return array
     * @throws \App\Exceptions\HotelBeadsException
     */
    public function getBoards($filters)
    {
        return $this->exceptionHandler->handle(function () use ($filters) {
            return $this->api->getBoards($filters);
        });
    }


    /**
     * @param $filters
     * @return array
     * @throws \App\Exceptions\HotelBeadsException
     */
    public function getChains($filters)
    {
        return $this->exceptionHandler->handle(function () use ($filters) {
            return $this->api->getChains($filters);
        });
    }

    /**
     * @param $filters
     * @return HotelBedsDictionaryResponse
     * @throws \App\Exceptions\HotelBeadsException
     */
    public function getFacilities($filters)
    {
        $apiData = $this->exceptionHandler->handle(function () use ($filters) {
            return $this->api->getFacilities($filters);
        });
        $result = [];
        if (!empty($apiData['facilities'])) {
            foreach ($apiData['facilities'] as $data) {
                $result[] = $this->hotelFactory->createHotelBedsStaticsFacility($data);
            }
        }

        return new HotelBedsDictionaryResponse(
            collect($result),
            $apiData['total']
        );
    }

    /**
     * @param $filters
     * @return array
     * @throws \App\Exceptions\HotelBeadsException
     */
    public function getFacilitygroups($filters)
    {
        return $this->exceptionHandler->handle(function () use ($filters) {
            return $this->api->getFacilitygroups($filters);
        });
    }

    public function getIssues($filters)
    {
        return $this->exceptionHandler->handle(function () use ($filters) {
            return $this->api->getIssues($filters);
        });
    }

    /**
     * @param $filters
     * @return array
     * @throws \App\Exceptions\HotelBeadsException
     */
    public function getPromotions($filters)
    {
        return $this->exceptionHandler->handle(function () use ($filters) {
            return $this->api->getPromotions($filters);
        });
    }

    /**
     * @param $filters
     * @return array
     * @throws \App\Exceptions\HotelBeadsException
     */
    public function getRooms($filters)
    {
        return $this->exceptionHandler->handle(function () use ($filters) {
            return $this->api->getRooms($filters);
        });
    }

    /**
     * @param $filters
     * @return array
     * @throws \App\Exceptions\HotelBeadsException
     */
    public function getSegments($filters)
    {
        return $this->exceptionHandler->handle(function () use ($filters) {
            return $this->api->getSegments($filters);
        });
    }

    /**
     * @param $filters
     * @return array
     * @throws \App\Exceptions\HotelBeadsException
     */
    public function getTerminals($filters)
    {
        return $this->exceptionHandler->handle(function () use ($filters) {
            return $this->api->getTerminals($filters);
        });
    }

    public function getImagetypes($filters)
    {
        return $this->exceptionHandler->handle(function () use ($filters) {
            return $this->api->getImagetypes($filters);
        });
    }

    /**
     * @param $filters
     * @return array
     * @throws \App\Exceptions\HotelBeadsException
     */
    public function getGroupcategories($filters)
    {
        return $this->exceptionHandler->handle(function () use ($filters) {
            return $this->api->getGroupcategories($filters);
        });
    }

    /**
     * @param $filters
     * @return array
     * @throws \App\Exceptions\HotelBeadsException
     */
    public function getRatecomments($filters)
    {
        return $this->exceptionHandler->handle(function () use ($filters) {
            return $this->api->getRatecomments($filters);
        });
    }

    /**
     * @param $filters
     * @return array
     * @throws \App\Exceptions\HotelBeadsException
     */
    public function getRatecommentdetails($filters)
    {
        return $this->exceptionHandler->handle(function () use ($filters) {
            return $this->api->getRatecommentdetails($filters);
        });
    }

    /**
     * @param $filters
     * @return mixed
     * @throws \App\Exceptions\HotelBeadsException
     */
    public function getLanguages($filters)
    {
        return $this->exceptionHandler->handle(function () use ($filters) {
            return $this->api->getLanguages($filters);
        });
    }


    public function getAvailabilityByHotels(HotelBedsHotelsAvailabilityRequest $request)
    {
        $apiData = $this->exceptionHandler->handle(function () use ($request) {
            return $this->api->getAvailabilityByHotels($request);
        });
        $hotelsList = collect();
        if (!empty($apiData['hotels']['hotels'])) {
            $hotelCodes = collect($apiData['hotels']['hotels'])->pluck('code')->toArray();
            $hotelStaticInfoList = $this->repository->getHotelListStaticInfo($hotelCodes);
            foreach ($apiData['hotels']['hotels'] as $data) {
                $hotelsList->push($this->hotelFactory->createHotelListItem($data, $hotelStaticInfoList[$data['code']] ?? null , $request->networkId));
            }
        }

        return $hotelsList;
    }

    /**
     * @param $requestBody
     * @param $networkId
     * @return HotelBedsHotelDetail
     * @throws \App\Exceptions\HotelBeadsException
     */
    public function checkBookingRates($requestBody, $networkId)
    {
        $apiData = $this->exceptionHandler->handle(function () use ($requestBody, $networkId) {
            return $this->api->checkBookingRates($requestBody);
        });
        $hotelStaticInfo = $this->repository->getHotelStaticInfo($apiData['hotel']['code']);

        return $this->hotelFactory->createHotelDetailItem($apiData['hotel'], $hotelStaticInfo, $networkId);
    }

    public function checkRatesBySearchCode(HotelBedsSearchCode $searchCode, $networkId)
    {
        $apiData = $this->exceptionHandler->handle(function () use ($searchCode, $networkId) {
            return $this->api->checkRates($searchCode);
        });
        if (!empty($apiData['hotel'])) {
            $hotelStaticInfo = $this->repository->getHotelStaticInfo($apiData['hotel']['code']);
            return $this->hotelFactory->createHotelDetailItem($apiData['hotel'], $hotelStaticInfo, $networkId);
        }

        return null;
    }

    public function checkRatesForRoomOffer(UserContext $context, $offerCode)
    {
        $apiData = $this->exceptionHandler->handle(function () use ($offerCode, $context) {
            return $this->api->checkRatesForRoomOffer($offerCode);
        });

        if (!empty($apiData['hotel'])) {
            $hotelStaticInfo = $this->repository->getHotelStaticInfo($apiData['hotel']['code']);
            return $this->hotelFactory->createHotelRoomOfferItem($context, $hotelStaticInfo, $apiData['hotel']);
        }

        return null;
    }

    /**
     * @param $requestBody
     * @return mixed
     * @throws \App\Exceptions\HotelBeadsException
     */
    public function bookings($requestBody, $networkId)
    {
        $apiData = $this->exceptionHandler->handle(function () use ($requestBody, $networkId) {
            return $this->api->bookings($requestBody);
        });

        return $this->hotelFactory->createHotelBookingItem($apiData['booking'], $networkId);
    }

    /**
     * @param $booking_reference
     * @return array
     * @throws \App\Exceptions\HotelBeadsException
     */
    public function getBookingsDetail($booking_reference)
    {
        return $this->exceptionHandler->handle(function () use ($booking_reference) {
            return $this->api->getBookingsDetail($booking_reference);
        });
    }

    /**
     * @param $filters
     * @return array
     * @throws \App\Exceptions\HotelBeadsException
     */
    public function getBookings($filters)
    {
        return $this->exceptionHandler->handle(function () use ($filters) {
            return $this->api->getBookings($filters);
        });
    }

    /**
     * @param $params
     * @return array
     * @throws \App\Exceptions\HotelBeadsException
     */
    public function deleteBookings($params)
    {
        return $this->exceptionHandler->handle(function () use ($params) {
            return $this->api->deleteBookings($params);
        });
    }

    /**
     * @param $body
     * @return array
     * @throws \App\Exceptions\HotelBeadsException
     */
    public function putBookings($body)
    {
        return $this->exceptionHandler->handle(function () use ($body) {
            return $this->api->putBookings($body);
        });
    }
}