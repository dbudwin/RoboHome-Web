<?php

namespace Tests\Browser;

use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DevicesTest extends DuskTestCase
{
    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
    }

    public function testDevices_GivenUserLoggedIn_VerifyPath(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit('/devices')
                ->assertPathIs('/devices');
        });
    }

    public function testDevices_GivenUserLoggedIn_SeesTitle(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit('/devices')
                ->assertTitle('RoboHome | Devices');
        });
    }

    public function testDevices_GivenUserLoggedIn_SeesLogoutLink(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit('/devices')
                ->assertSeeLink('Logout');
        });
    }

    public function testDevices_GivenUserLoggedIn_SeesHeaderWithUserName(): void
    {
        $this->browse(function (Browser $browser) {
            $userName = $this->user->name;
            $browser
                ->loginAs($this->user)
                ->visit('/devices')
                ->assertSeeIn('@devices-table-header', "$userName's Controllable Devices");
        });
    }

    public function testDevices_GivenUserLoggedIn_ClicksLogout_RedirectToIndexPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit('/devices')
                ->clickLink('Logout')
                ->assertPathIs('/');
        });
    }

    public function testDevices_GivenUserLoggedIn_ClicksAddDeviceButton_OpensAddDeviceModal(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit('/devices')
                ->press('Add Device')
                ->whenAvailable('#add-device-modal', function ($modal) {
                    $modal
                        ->assertSeeIn('@add-device-header', 'Add New Device')
                        ->assertSeeIn('@add-device-form', 'Device Name')
                        ->assertSeeIn('@add-device-form', 'Device Description')
                        ->assertSeeIn('@add-device-form', 'On Code')
                        ->assertSeeIn('@add-device-form', 'Off Code')
                        ->assertSeeIn('@add-device-form', 'Pulse Length')
                        ->assertSeeIn('@add-device-form', 'Close')
                        ->assertSeeIn('@add-device-form', 'Add Device');
                });
        });
    }

    public function testDevices_GivenUserLoggedIn_AddsDevice_DeviceAddedToPage(): void
    {
        $this->browse(function (Browser $browser) {
            $deviceName = self::$faker->word();
            $deviceDescription = self::$faker->sentence();
            $onCode = self::$faker->randomNumber();
            $offCode = self::$faker->randomNumber();
            $pulseLength = self::$faker->randomNumber();

            $this->loginAndAddDevice($browser, $deviceName, $deviceDescription, $onCode, $offCode, $pulseLength)
                ->assertSeeIn('@success-flash-message', "Device '$deviceName' was successfully added!")
                ->assertSeeIn('@devices-table', $deviceName)
                ->assertSeeIn('@devices-table', $deviceDescription)
                ->assertSeeIn('@devices-table', 'On')
                ->assertSeeIn('@devices-table', 'Off')
                ->press('@modify-device-button')
                ->assertSeeIn('@modify-device-dropdown', 'Edit')
                ->assertSeeIn('@modify-device-dropdown', 'Delete');
        });
    }

    public function testDevices_GivenUserLoggedIn_AddsDuplicatedDevice(): void
    {
        $this->browse(function (Browser $browser) {
            $deviceName = self::$faker->word();
            $deviceDescription = self::$faker->sentence();
            $onCode = self::$faker->randomNumber();
            $offCode = self::$faker->randomNumber();
            $pulseLength = self::$faker->randomNumber();

            $this->loginAndAddDevice($browser, $deviceName, $deviceDescription, $onCode, $offCode, $pulseLength)
                ->assertSeeIn('@success-flash-message', "Device '$deviceName' was successfully added!")
                ->assertSeeIn('@devices-table', $deviceName)
                ->assertSeeIn('@devices-table', $deviceDescription)
                ->assertSeeIn('@devices-table', 'On')
                ->assertSeeIn('@devices-table', 'Off')
                ->press('@modify-device-button')
                ->assertSeeIn('@modify-device-dropdown', 'Edit')
                ->assertSeeIn('@modify-device-dropdown', 'Delete');

            $this->loginAndAddDevice($browser, $deviceName, $deviceDescription, $onCode, $offCode, $pulseLength)
                ->assertSeeIn('@danger-flash-message', "Device '$deviceName' has existed!")
                ->assertSeeIn('@devices-table', $deviceName)
                ->assertSeeIn('@devices-table', $deviceDescription)
                ->assertSeeIn('@devices-table', 'On')
                ->assertSeeIn('@devices-table', 'Off')
                ->press('@modify-device-button')
                ->assertSeeIn('@modify-device-dropdown', 'Edit')
                ->assertSeeIn('@modify-device-dropdown', 'Delete');
        });
    }

    public function testDevices_GivenUserLoggedIn_DeletesExistingDevice_DeviceRemovedFromPage(): void
    {
        $this->browse(function (Browser $browser) {
            $deviceName = self::$faker->word();
            $deviceDescription = self::$faker->sentence();
            $onCode = self::$faker->randomNumber();
            $offCode = self::$faker->randomNumber();
            $pulseLength = self::$faker->randomNumber();

            $this->loginAndAddDevice($browser, $deviceName, $deviceDescription, $onCode, $offCode, $pulseLength)
                ->press('@modify-device-button')
                ->clickLink('Delete')
                ->press('OK')
                ->assertSeeIn('@success-flash-message', "Device '$deviceName' was successfully deleted!")
                ->assertDontSeeIn('@devices-table', $deviceName)
                ->assertDontSeeIn('@devices-table', $deviceDescription)
                ->assertDontSee('@devices-table', 'On')
                ->assertDontSee('@devices-table', 'Off');
        });
    }

    public function testDevices_GivenUserLoggedIn_DeletesExistingDevice_UserCancelsDeleteOperation(): void
    {
        $this->browse(function (Browser $browser) {
            $deviceName = self::$faker->word();
            $deviceDescription = self::$faker->sentence();
            $onCode = self::$faker->randomNumber();
            $offCode = self::$faker->randomNumber();
            $pulseLength = self::$faker->randomNumber();

            $this->loginAndAddDevice($browser, $deviceName, $deviceDescription, $onCode, $offCode, $pulseLength)
                ->press('@modify-device-button')
                ->clickLink('Delete')
                ->press('Cancel')
                ->assertDontSeeIn('@success-flash-message', "Device '$deviceName' was successfully deleted!")
                ->assertSeeIn('@devices-table', $deviceName)
                ->assertSeeIn('@devices-table', $deviceDescription);
        });
    }

    public function testDevices_GivenUserLoggedIn_ClicksEditDeviceButton_OpensEditDeviceModal(): void
    {
        $this->browse(function (Browser $browser) {
            $deviceName = self::$faker->word();
            $deviceDescription = self::$faker->sentence();
            $onCode = self::$faker->randomNumber();
            $offCode = self::$faker->randomNumber();
            $pulseLength = self::$faker->randomNumber();

            $this->loginAndAddDevice($browser, $deviceName, $deviceDescription, $onCode, $offCode, $pulseLength)
                ->press('@modify-device-button')
                ->clickLink('Edit')
                ->whenAvailable('#edit-device-modal', function ($modal) use ($deviceName, $deviceDescription, $onCode, $offCode, $pulseLength) {
                    $modal
                        ->assertSeeIn('@edit-device-header', 'Update Existing Device')
                        ->assertSeeIn('@edit-device-form', 'Device Name')
                        ->assertSeeIn('@edit-device-form', 'Device Description')
                        ->assertSeeIn('@edit-device-form', 'On Code')
                        ->assertSeeIn('@edit-device-form', 'Off Code')
                        ->assertSeeIn('@edit-device-form', 'Pulse Length')
                        ->assertSeeIn('@edit-device-form', 'Close')
                        ->assertSeeIn('@edit-device-form', 'Update Device')
                        ->assertInputValue('input[name=name]', $deviceName)
                        ->assertInputValue('input[name=description]', $deviceDescription)
                        ->assertInputValue('input[name=on_code]', $onCode)
                        ->assertInputValue('input[name=off_code]', $offCode)
                        ->assertInputValue('input[name=pulse_length]', $pulseLength);
                });
        });
    }

    public function testDevices_GivenUserLoggedIn_EditsDevice_DevicesUpdatedOnPage(): void
    {
        $this->browse(function (Browser $browser) {
            $deviceName = self::$faker->word();
            $deviceDescription = self::$faker->sentence();
            $onCode = self::$faker->randomNumber();
            $offCode = self::$faker->randomNumber();
            $pulseLength = self::$faker->randomNumber();
            $newDeviceName = self::$faker->word();
            $newDeviceDescription = self::$faker->sentence();
            $newOnCode = self::$faker->randomNumber();
            $newOffCode = self::$faker->randomNumber();
            $newPulseLength = self::$faker->randomNumber();

            $this->loginAndAddDevice($browser, $deviceName, $deviceDescription, $onCode, $offCode, $pulseLength)
                ->press('@modify-device-button')
                ->clickLink('Edit')
                ->whenAvailable('#edit-device-modal', function ($modal) use ($newDeviceName, $newDeviceDescription, $newOnCode, $newOffCode, $newPulseLength) {
                    $modal
                        ->value('input[name=name]', $newDeviceName)
                        ->value('input[name=description]', $newDeviceDescription)
                        ->value('input[name=on_code]', $newOnCode)
                        ->value('input[name=off_code]', $newOffCode)
                        ->value('input[name=pulse_length]', $newPulseLength)
                        ->press('#updateDeviceButton');
                })
                ->waitUntilMissing('#edit-device-modal')
                ->assertSeeIn('@success-flash-message', "Device '$newDeviceName' was successfully updated!")
                ->assertSeeIn('@devices-table', $newDeviceName)
                ->assertSeeIn('@devices-table', $newDeviceDescription)
                ->assertSeeIn('@devices-table', 'On')
                ->assertSeeIn('@devices-table', 'Off');
        });
    }

    public function testDevices_GivenUserLoggedIn_HasCopyrightNotice(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit('/devices')
                ->assertSeeIn('@copyright-notice', '© ' . gmdate('Y') . ' by RoboHome');
        });
    }

    public function testDevices_GivenUserNotLoggedIn_HasCopyrightNotice(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/devices')
                ->assertSeeIn('@copyright-notice', '© ' . gmdate('Y') . ' by RoboHome');
        });
    }

    private function createUser(): User
    {
        $user = factory(User::class)->create();

        return $user;
    }

    private function loginAndAddDevice(Browser $browser, string $deviceName, string $deviceDescription, int $onCode, int $offCode, int $pulseLength) : Browser
    {
        $browser
            ->loginAs($this->user)
            ->visit('/devices')
            ->click('@open-add-device-button-modal')
            ->whenAvailable('#add-device-modal', function ($modal) use ($deviceName, $deviceDescription, $onCode, $offCode, $pulseLength) {
                $modal
                    ->value('input[name=name]', $deviceName)
                    ->value('input[name=description]', $deviceDescription)
                    ->value('input[name=on_code]', $onCode)
                    ->value('input[name=off_code]', $offCode)
                    ->value('input[name=pulse_length]', $pulseLength)
                    ->press('#addDeviceButton');
            })
            ->waitUntilMissing('#add-device-modal');

        return $browser;
    }
}
