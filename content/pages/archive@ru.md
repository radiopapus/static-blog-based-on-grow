---
$title@: Archive
image: /static/images/viktorzharina.jpg
description: Архив старых записей
$titles:
  nav@: Archive
$order: 1
---
<h3>{{_('Archive')}}</h3>
<ol start="11">
    {% for post in g.docs('posts', locale=doc.locale)[0:-11]|reverse %}
          {% if post.title != None %}
            <li><a href="{{post.url.path}}">{{_(post.title)}}</a></li>

        {% endif %}
    {% endfor %}
</ol>
