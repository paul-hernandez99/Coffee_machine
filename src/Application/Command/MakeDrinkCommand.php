<?php

namespace Pdpaola\CoffeeMachine\Application\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Pdpaola\CoffeeMachine\Domain\DrinkOrder;
use Pdpaola\CoffeeMachine\Domain\DrinkValidator;
use Pdpaola\CoffeeMachine\Domain\DrinkPricer;

use Pdpaola\CoffeeMachine\Infra\Persistence\OrderRepository;
use Pdpaola\CoffeeMachine\Infra\Persistence\MysqlPdoClient;


class MakeDrinkCommand extends Command
{
    protected static $defaultName = 'app:order-drink';

    protected function configure()
    {
        $this->addArgument(
            'drink-type',
            InputArgument::REQUIRED,
            'The type of the drink. (Tea, Coffee or Chocolate)'
        );

        $this->addArgument(
            'money',
            InputArgument::REQUIRED,
            'The amount of money given by the user'
        );

        $this->addArgument(
            'sugars',
            InputArgument::OPTIONAL,
            'The number of sugars you want. (0, 1, 2)',
            0
        );

        $this->addOption(
            'extra-hot',
            'e',
            InputOption::VALUE_NONE,
            $description = 'If the user wants to make the drink extra hot'
        );
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $drinkType = strtolower($input->getArgument('drink-type'));
        $money = $input->getArgument('money');
        $sugars = $input->getArgument('sugars');
        $extraHot = $input->getOption('extra-hot');

        // Validate drink
        if (!DrinkValidator::validateDrinkType($drinkType)) {
            $output->writeln('The drink type should be tea, coffee or chocolate.');
            return;
        }

        // Validate sugars
        if (!DrinkValidator::validateSugars($sugars)) {
            $output->writeln('The number of sugars should be between 0 and 2.');
            return;
        }

        // Check price
        $price = DrinkPricer::getPrice($drinkType);
        if ($money < $price) {
            $output->writeln("The $drinkType costs $price.");
            return;
        }

        $order = new DrinkOrder($drinkType, $sugars, $extraHot);
        $repo = new OrderRepository(MysqlPdoClient::getPdo());

        $repo->savePrices();
        $repo->saveOrder($order);
        $this->viewTotalProfit($repo->drinkMoney(), $output);

        $message = "You have ordered a $drinkType";
        if ($extraHot) {
            $message .= " extra hot";
        }
        if ($sugars > 0) {
            $message .= " with $sugars sugars (stick included)";
        }
        $output->writeln($message);
    }

    public function viewTotalProfit($results,OutputInterface $output) {
        if ($results) {
            $output->writeln('');
            $output->writeln('|Drink|Money|');
            $output->writeln('|---|---|');
            foreach ($results as $row) {
                $output->writeln(sprintf("|%s|%s|", ucfirst($row['drink_type']), $row['Money']));
            }
        } else {
            $output->writeln('No orders found.');
        }
    }

}

