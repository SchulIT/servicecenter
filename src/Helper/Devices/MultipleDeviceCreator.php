<?php

declare(strict_types=1);

namespace App\Helper\Devices;

use App\Entity\Device;
use App\Entity\DeviceType;
use App\Entity\Room;

class MultipleDeviceCreator {

    /**
     * @return Device[]
     */
    public function createDevices(Room $room, DeviceType $deviceType, string $name, int $quantity, int $startIndex, int $padLength = 0): array {
        $devices = [ ];

        for($currentIndex = 0; $currentIndex < $quantity; ++$currentIndex) {
            $device = (new Device())
                ->setType($deviceType)
                ->setRoom($room);

            $formattedIndex = $padLength > 0 ? str_pad("" .$currentIndex, $padLength, "0", STR_PAD_LEFT) : $currentIndex;

            $device->setName(
                str_replace(
                    ['%index%', '%room%'],
                    [$formattedIndex, $room->getName()],
                    $name
                )
            );

            $devices[] = $device;
        }

        return $devices;
    }
}
