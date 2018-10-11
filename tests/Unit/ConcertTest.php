<?php

namespace Tests\Unit;

use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ConcertTest extends TestCase
{

    use DatabaseMigrations;

    public function testCanGetFormattedDate()
    {
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2016-12-01 8:00pm')
        ]);

        $this->assertEquals('December 1, 2016', $concert->formatted_date);
    }

    public function testCanGetFormattedStartTime()
    {
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2016-12-01 17:00:00')
        ]);
        $this->assertEquals('5:00pm', $concert->formatted_start_time);
    }

    public function testCanGetTicketPriceInDollars()
    {
        $concert = factory(Concert::class)->make([
            'ticket_price' => 6750
        ]);
        $this->assertEquals('67.50', $concert->ticket_price_in_dollars);
    }

    public function testConcertsWithPublishedAtDateArePublished()
    {
        $publishedConcertA = factory(Concert::class)->states('published')->create();
        $publishedConcertB = factory(Concert::class)->states('published')->create();
        $unpublishedConcert = factory(Concert::class)->states('unpublished')->create();

        $publishedConcerts = Concert::published()->get();

        $this->assertTrue($publishedConcerts->contains($publishedConcertA));
        $this->assertTrue($publishedConcerts->contains($publishedConcertB));
        $this->assertFalse($publishedConcerts->contains($unpublishedConcert));
    }
}
