
<div class="css-columns-2">
    <h2>Downloads &amp; Data APIs</h2>

    <div style="display: inline-block">
        <h3>Dissemination Material</h3>
        <p><?php echo $this->Html->link('Course Registry Flyer',
                '/files/DH_Course_Registry_FLyer.pdf', array(
                'target' => '_blank')); ?></p>
    </div>

    <div style="display: inline-block">
        <h3>Video Lectures</h3>
        <p><?php echo $this->Html->link('Course Registry Metadathon',
                'http://videolectures.net/DHcourse2018_paris/', array(
                    'target' => '_blank')); ?>, a workshop held during the DARIAH annual meeting, 2018 in Paris. </p>
        <p>Tutorials for using the DH Course Registry. Watch the <?php echo $this->Html->link('playlist on Youtube',
                'https://www.youtube.com/watch?v=pvFKq67-21I&list=PLlKmS5dTMgw0u-Fvz_MC_QQNkMAPeLT5E', array(
                    'target' => '_blank')); ?>.</p>
    </div>

    <div style="display: inline-block">
        <h3>API, Data Download and Embedding</h3>
        <h4>JSON &amp; XML Views</h4>
        <p>
            We have a rudimentary API available, which either serves a full list of the course database
            or a single course. The data is available as JSON or XML.
            To render the data in the format of interest, one just needs to add <em>.json</em> or
            <em>.xml</em> to the end of the URL string.
            Use <em>/courses/index</em> for a full list of courses or
            <em>/courses/view/[id]</em> to view a single course.
        </p>
        <p>
            <?php echo $this->Html->link(Router::url('/courses/index.json', true),
                '/courses/index.json', array('target' => '_blank')); ?><br>
            <?php echo $this->Html->link(Router::url('/courses/index.xml', true),
                '/courses/index.xml', array('target' => '_blank')); ?><br>
            Single course format: <em><?php echo Router::url('/courses/view/[id].[format]'); ?></em><br>
            <?php echo $this->Html->link(Router::url('/courses/view/123.json', true),
                '/courses/view/123.json', array('target' => '_blank')); ?>.
        </p>
        <p>
            The courses listed in the <em>index</em> views contain only recent, actively maintained data.
            Currently, there is no API available to list historical courses, which still exist in the database
            but have been removed from display after their expiration.
        </p>

    </div>
    <div style="display: inline-block">
        <h3>Iframe Embedding</h3>
        <p>
            The course registry can be embedded into any other website by using an iframe.
            The additional route parameter "iframe" in the URL string will cause the layout to render
            without the upper logo bar and tabbing menu.
            Thus, it will integrate seamlessly into foreign institution pages.
        </p>
        <p>
            The basic URL for iframe embedding is:<br>
            <?php echo $this->Html->link(Router::url('/iframe', $full = true), '/iframe'); ?>
        </p>
        <p>
            Using the code snippet provided through the generator form,
            the iframe will expand to 100% of the parent container.
            The height is adjusted by some javascript from the page loaded within the iframe,
            thus you have to keep the provided id.
        </p>
    </div>
    <div style="display:inline-block">
        <h3>URL Filter Parameters</h3>
        <p>
            You can also pass filter parameters using the URL, which is handy to filter
            for a specific country or discipline in the iframe-embedded application or
            to provide a link with preset filter settings.
            By adding <em>[key:value]</em> pairs to the end of the URL string, the results will be filtered
            for a particular numeric ID-value of an attribute field.
            Parameters can only be passed to the <em>index</em> method: <br>
            <?php echo $this->Html->link(Router::url('/iframe/courses/index/country_id:1', $full = true), '/iframe/courses/index/country_id:1'); ?>
        </p>
        <p>
            The available filter parameters are:
        </p>
        <ul>
            <li><em>country_id</em></li>
            <li><em>city_id</em></li>
            <li><em>institution_id</em></li>
            <li><em>course_type_id</em> (Education)</li>
            <li><em>tadirah_object_id</em></li>
            <li><em>tadirah_technique_id</em></li>
            <li><em>discipline_id</em></li>
        </ul>
        <p>
            You can lookup the id of any attribute of interest
            using the URL generator below.
        </p>
    </div>

    <div style="display:inline-block">
        <h3>Data API, Filter URL &amp; Embedding Code Generator</h3>
        <?php echo $this->element('filter_generator'); ?>
    </div>

</div>

