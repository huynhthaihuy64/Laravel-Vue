<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\Drivers\Facebook\Extensions\ButtonTemplate;
use BotMan\Drivers\Facebook\Extensions\ElementButton;

class OnConversation extends Conversation
{
    protected $firstname;

    protected $email;

    public function askFirstname()
    {
        $this->ask('Hello! What is your firstname?', function (Answer $answer) {
            $firstname = $answer->getText();
            $this->say('Nice to meet you ' . $firstname);

            $categories = Menu::select('id', 'name')->get();
            $categoriesButtons = $categories->map(function ($category) {
                return Button::create($category['name'])->value($category['id']);
            })->all();

            $question = Question::create('Please select a topic:')
                ->fallback('Unable to ask question')
                ->callbackId('select_product')
                ->addButtons([
                    Button::create('Contact')->value('contact'),
                    Button::create('About')->value('about'),
                    Button::create('Category')->value('category'),
                ]);

            $this->ask($question, function (Answer $answer) use ($categoriesButtons) {
                $selected = $answer->getValue();
                $this->say('You selected: ' . $selected);
                if ($selected === 'contact') {
                    $this->say('Please wait a moment...');
                    $this->getBot()->typesAndWaits(2);
                    $this->say('<a href="' . env('APP_URL') . '/contact" target="_blank">Click here to redirect Contact page </a>');
                } elseif ($selected === 'about') {
                    $this->say('You selected: ' . $selected);
                    $this->ask('Please wait a moment...', function (Answer $answer) {
                    });
                    $this->getBot()->typesAndWaits(2);
                    $this->say('<a href="' . env('APP_URL') . '/about" target="_blank">Click here to redirect About page </a>');
                } else {
                    $chooseTopics = Question::create('Please select a topic:')
                        ->fallback('Unable to ask question')
                        ->callbackId('select_product')
                        ->addButtons($categoriesButtons);
                    $this->ask(
                        $chooseTopics,
                        function (Answer $answer) {
                            $menu = Menu::find($answer->getValue());
                            $this->say('You selected: ' . $menu->name);
                            $this->say('Please wait a moment...');
                            $this->getBot()->typesAndWaits(2);
                            $this->say('<a href="' . env('APP_URL') . '/categories/' . $menu->id . '" target="_blank">' . $menu->name . '</a>');
                        }
                    );
                }
            });
        });
    }

    public function askEmail()
    {
        $this->ask('One more thing - what is your email?', function (Answer $answer) {
            $this->email = $answer->getText();

            $this->say('Great - that is all we need, ' . $this->firstname);
        });
    }

    public function run()
    {
        $this->askFirstname();
    }
}
