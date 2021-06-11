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

/* core/themes/stable/templates/admin/maintenance-task-list.html.twig */
class __TwigTemplate_d295c51d9e84c36f53ef9cb84ea4ac4537d10bf7d78276020265c0f7621c475d extends \Twig\Template
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
        // line 15
        echo "<h2 class=\"visually-hidden\">";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Installation tasks"));
        echo "</h2>
<ol class=\"task-list\">
";
        // line 17
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["tasks"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["task"]) {
            // line 18
            echo "  <li";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["task"], "attributes", [], "any", false, false, true, 18), 18, $this->source), "html", null, true);
            echo ">
    ";
            // line 19
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["task"], "item", [], "any", false, false, true, 19), 19, $this->source), "html", null, true);
            echo "
    ";
            // line 20
            if (twig_get_attribute($this->env, $this->source, $context["task"], "status", [], "any", false, false, true, 20)) {
                echo "<span class=\"visually-hidden\"> (";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["task"], "status", [], "any", false, false, true, 20), 20, $this->source), "html", null, true);
                echo ")</span>";
            }
            // line 21
            echo "  </li>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['task'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 23
        echo "</ol>
";
    }

    public function getTemplateName()
    {
        return "core/themes/stable/templates/admin/maintenance-task-list.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  71 => 23,  64 => 21,  58 => 20,  54 => 19,  49 => 18,  45 => 17,  39 => 15,);
    }

    public function getSourceContext()
    {
        return new Source("", "core/themes/stable/templates/admin/maintenance-task-list.html.twig", "/app/web/core/themes/stable/templates/admin/maintenance-task-list.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("for" => 17, "if" => 20);
        static $filters = array("t" => 15, "escape" => 18);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['for', 'if'],
                ['t', 'escape'],
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
