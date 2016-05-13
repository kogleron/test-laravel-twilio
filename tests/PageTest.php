<?php

class PageTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testPage()
    {
        $this->visit('/')
            ->see('Twilio');
    }
}
