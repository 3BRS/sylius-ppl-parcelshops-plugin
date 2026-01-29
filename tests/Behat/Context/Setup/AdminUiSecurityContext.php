<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusPplParcelshopsPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Mink\Mink;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

/**
 * Security context that logs in via HTTP for JavaScript tests.
 * This is needed because Panther browser doesn't share sessions with CLI Behat.
 */
final class AdminUiSecurityContext implements Context
{
    /**
     * @param UserRepositoryInterface<AdminUserInterface> $userRepository
     */
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private ExampleFactoryInterface $userFactory,
        private UserRepositoryInterface $userRepository,
        private Mink $mink,
        private string $baseUrl,
    ) {
    }

    /**
     * @Given I am logged in as an administrator
     */
    public function iAmLoggedInAsAnAdministrator(): void
    {
        $user = $this->userFactory->create(['email' => 'sylius@example.com', 'password' => 'sylius', 'api' => true]);
        \assert($user instanceof AdminUserInterface);
        $this->userRepository->add($user);

        $this->sharedStorage->set('administrator', $user);

        // Perform actual HTTP login in the browser
        $this->loginViaHttp('sylius@example.com', 'sylius');
    }

    private function loginViaHttp(string $email, string $password): void
    {
        $session = $this->mink->getSession();

        // Visit the admin login page
        $loginUrl = rtrim($this->baseUrl, '/') . '/admin/login';
        $session->visit($loginUrl);

        // Wait for page to load (wait longer since assets need to load)
        sleep(2);

        $page = $session->getPage();

        // Debug: show page content if login form not found
        $usernameField = $page->findField('_username');
        if ($usernameField === null) {
            $pageContent = $page->getContent();
            $pageUrl = $session->getCurrentUrl();

            // Check if page has an error
            if (str_contains($pageContent, 'error') || str_contains($pageContent, '500')) {
                throw new \RuntimeException(sprintf(
                    'Login page seems to have an error. URL: %s, Content: %s',
                    $pageUrl,
                    substr($pageContent, 0, 2000),
                ));
            }

            // Check if we're on a different page (like a 404)
            if (!str_contains($pageUrl, 'login')) {
                throw new \RuntimeException(sprintf(
                    'Not on login page. Current URL: %s',
                    $pageUrl,
                ));
            }

            throw new \RuntimeException(sprintf(
                'Username field not found on login page. URL: %s, Has form: %s',
                $pageUrl,
                $page->find('css', 'form') !== null ? 'yes' : 'no',
            ));
        }
        $usernameField->setValue($email);

        $passwordField = $page->findField('_password');
        if ($passwordField === null) {
            throw new \RuntimeException('Password field not found on login page');
        }
        $passwordField->setValue($password);

        // Submit the form - try multiple selectors
        $submitButton = $page->findButton('Login');
        if ($submitButton === null) {
            $submitButton = $page->find('css', 'button[type="submit"]');
        }
        if ($submitButton === null) {
            $submitButton = $page->find('css', 'input[type="submit"]');
        }
        if ($submitButton === null) {
            throw new \RuntimeException('Login button not found');
        }
        $submitButton->press();

        // Wait for redirect to complete
        sleep(2);
    }
}
