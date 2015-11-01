<?php

use Illuminate\Routing\UrlGenerator;
use Illuminate\Routing\RouteCollection;
use Illuminate\Http\Request;

use Ycore\Html\Factories\AbstractHtml;

use Ycore\Html\HtmlBuilder;
use Ycore\Html\Styles\Bootstrap4;

use Mockery as m;

class HtmlBuilderTest extends TestCase
{
    /**
    * Setup the test environment.
    */
    public function setUp()
    {
        $this->urlGenerator = new UrlGenerator(new RouteCollection(), Request::create('/foo', 'GET'));
        $this->htmlMarkup  = new AbstractHtml();
        $this->htmlBuilder = new HtmlBuilder(new Bootstrap4());
    }

    /**
    * Destroy the test environment.
    */
    public function tearDown()
    {
        m::close();
    }

    public function testAbstractMarkup()
    {
        $htmlMarkup = $this->htmlMarkup;

        $this->assertEquals('<p>Here a paragraph.</p><p class="my_class" id="aId">Another paragraph with a class and an &quot;id&quot;.</p>Concatenation is done with the method _<br />Without content, self closing tag is generated :<br /><hr /><p>Yes, I like chaining coding style !</p>',
        (string) $htmlMarkup
            ->P('Here a paragraph.')
            ->P(array(
                    'class' => 'my_class',
                    'id' => 'aId',
                ), 'Another paragraph with a class and an "id".'
            )
            ->_('Concatenation is done with the method _')
            ->BR()
            ->_('Without content, self closing tag is generated :')
            ->BR()->HR()
            ->P('Yes, I like chaining coding style !')
        );

        $this->assertEquals('<a href="http://google.fr">google.fr</a><p>The variable &quot;$htmlMarkup&quot; is callable an give you a new &quot;htmlMarkup&quot; instance.</p><div><b>A new &quot;htmlMarkup&quot; process starts calling &quot;$htmlMarkup()&quot;</b><p>And I can continue chaining coding style</p><b>Shortcut of concatenation starting a new instance <i>foo bar</i></b></div>',
        (string) $htmlMarkup
            ->A(array('href' => 'http://google.fr'), 'google.fr')
            ->P('The variable "$htmlMarkup" is callable an give you a new "htmlMarkup" instance.')
            ->DIV($htmlMarkup()
                ->B('A new "htmlMarkup" process starts calling "$htmlMarkup()"')
                ->P('And I can continue chaining coding style')
                ->B($htmlMarkup('Shortcut of concatenation starting a new instance ')
                    ->I('foo bar')
                )
            )
        );

        $this->assertEquals('<p><strong>bold here, </strong><i>italic after</i></p><div class="my_class" id="aId">text concat <span>in a &quot;span&quot; <i>with italic</i></span></div><div><p>We can interrupt the chaining</p></div><br />Bye',
        (string) $htmlMarkup
            ->P($htmlMarkup()
                ->STRONG('bold here, ')
                ->I('italic after')
            )
            ->DIV(array('class' => 'my_class', 'id' => 'aId'),
                $htmlMarkup('text concat ')
                    ->SPAN($htmlMarkup('in a "span" ')
                        ->I('with italic')
                    )
            )
            ->DIV($htmlMarkup()
                ->P('We can interrupt the chaining')
            )
            ->BR()
            ->_('Bye')
        );

        $this->assertEquals('<form method="POST" action="/auth/login"><fieldset class="form-group"><label>Email</label><input class="form-control" type="email" name="email" placeholder="Enter your login e-mail adress" /></fieldset></form>',
        (string) $htmlMarkup
            ->FORM(["method" => "POST", "action" => "/auth/login"], $htmlMarkup()
                ->FIELDSET(["class" => "form-group"], $htmlMarkup()
                    ->LABEL("Email")
                    ->INPUT(["class" => "form-control", "type" => "email", "name" => "email", "placeholder" => "Enter your login e-mail adress"])
                )
            )
        );

    }


    public function testHtmlBuilder()
    {
        $htmlBuilder = $this->htmlBuilder;

        $this->assertEquals('<fieldset class="form-group"><label for="id1">Label1</label></fieldset>',
        (string) $htmlBuilder
            ->FIELDSET(
                $htmlBuilder->LABEL(['for' => 'id1'], 'Label1')
            )
        );

    }

}
