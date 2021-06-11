<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* core/themes/stable/templates/layout/install-page.html.twig */
class __TwigTemplate_5b15ecf60b8d9d27efb9f49ec526515cdecd36804cbfd2decf9c2181fda5f75c extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 12
        echo "  <div class=\"layout-container\">

    <header role=\"banner\">
      ";
        // line 15
        if ((($context["site_name"] ?? null) || ($context["site_slogan"] ?? null))) {
            // line 16
            echo "        <div class=\"name-and-slogan\">
          ";
            // line 17
            if (($context["site_name"] ?? null)) {
                // line 18
                echo "            <h1>";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["site_name"] ?? null), 18, $this->source), "html", null, true);
                echo "</h1>
          ";
            }
            // line 20
            echo "          ";
            if (($context["site_slogan"] ?? null)) {
                // line 21
                echo "            <div class=\"site-slogan\">";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["site_slogan"] ?? null), 21, $this->source), "html", null, true);
                echo "</div>
          ";
            }
            // line 23
            echo "        </div>";
            // line 24
            echo "      ";
        }
        // line 25
        echo "    </header>

    <main role=\"main\">
      ";
        // line 28
        if (($context["title"] ?? null)) {
            // line 29
            echo "        <h1>";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["title"] ?? null), 29, $this->source), "html", null, true);
            echo "</h1>
      ";
        }
        // line 31
        echo "      ";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "highlighted", [], "any", false, false, true, 31), 31, $this->source), "html", null, true);
        echo "
      ";
        // line 32
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "content", [], "any", false, false, true, 32), 32, $this->source), "html", null, true);
        echo "
    </main>

    ";
        // line 35
        if (twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "sidebar_first", [], "any", false, false, true, 35)) {
            // line 36
            echo "      <aside class=\"layout-sidebar-first\" role=\"complementary\">
        ";
            // line 37
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "sidebar_first", [], "any", false, false, true, 37), 37, $this->source), "html", null, true);
            echo "
      </aside>";
            // line 39
            echo "    ";
        }
        // line 40
        echo "
    ";
        // line 41
        if (twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "sidebar_second", [], "any", false, false, true, 41)) {
            // line 42
            echo "      <aside class=\"layout-sidebar-second\" role=\"complementary\">
        ";
            // line 43
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "sidebar_second", [], "any", false, false, true, 43), 43, $this->source), "html", null, true);
            echo "
      </aside>";
            // line 45
            echo "    ";
        }
        // line 46
        echo "
    ";
        // line 47
        if (twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "footer", [], "any", false, false, true, 47)) {
            // line 48
            echo "      <footer role=\"contentinfo\">
        ";
            // line 49
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "footer", [], "any", false, false, true, 49), 49, $this->source), "html", null, true);
            echo "
      </footer>
    ";
        }
        // line 52
        echo "
  </div>";
    }

    public function getTemplateName()
    {
        return "core/themes/stable/templates/layout/install-page.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  136 => 52,  130 => 49,  127 => 48,  125 => 47,  122 => 46,  119 => 45,  115 => 43,  112 => 42,  110 => 41,  107 => 40,  104 => 39,  100 => 37,  97 => 36,  95 => 35,  89 => 32,  84 => 31,  78 => 29,  76 => 28,  71 => 25,  68 => 24,  66 => 23,  60 => 21,  57 => 20,  51 => 18,  49 => 17,  46 => 16,  44 => 15,  39 => 12,);
    }

    public function getSourceContext()
    {
        return new Source("", "core/themes/stable/templates/layout/install-page.html.twig", "/app/web/core/themes/stable/templates/layout/install-page.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 15);
        static $filters = array("escape" => 18);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['if'],
                ['escape'],
                []
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
