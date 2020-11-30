<?php


namespace App\Components\Redemption\HotelBeds;


use App\Components\Redemption\HotelBeds\Api\Exceptions\HotelBedsApiSystemException;
use App\Components\Redemption\HotelBeds\Api\Exceptions\HotelBedsApiUserException;
use App\Exceptions\HotelBeadsException;

class HotelBedsExceptionHandler
{
    /**
     * @param callable $callBack
     * @param callable|null $customCatch
     * @return mixed
     * @throws HotelBeadsException
     */
    public function handle(callable $callBack, callable $customCatch = null)
    {
        try {
            return $callBack();
        }catch(HotelBedsApiSystemException $e){
            throw HotelBeadsException::SystemException($e);
        }catch(HotelBedsApiUserException $e){
            if ($customCatch){
                $customCatch($e); //if we want process some exceptions manually
            }
            throw HotelBeadsException::UserException($e->getMessage());
        }
    }
}