<?php

namespace App\Repositories;

use App\Device;
use Webpatser\Uuid\Uuid;

interface IDeviceRepository
{
    public function create(array $deviceProperties, int $userId): Device;
    public function get(int $id): Device;
    public function getForPublicId(Uuid $publicId): Device;
    public function update(int $id, array $deviceProperties): Device;
    public function delete(int $id): bool;
    public function name(int $id): string;
}
