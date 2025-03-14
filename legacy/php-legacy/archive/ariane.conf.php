<ol itemscope itemtype="https://schema.org/BreadcrumbList" class="breadcrumb">
    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
        <a itemprop="item" href="<?php echo $domain; ?>/">
            <span itemprop="name"><?php echo $domainwebsitename; ?></span>
        </a>
        <meta itemprop="position" content="1" />
    </li>
    <?php
    switch ($typefile)
    {
        case 'standard':
        {
        ?>
        &gt
        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <span itemprop="name"><?php echo $arianetitle; ?></span>
                <meta itemprop="position" content="2" />
        </li>
        <?php
        break;
        }
        case 'my':
        {
        ?>
        &gt
        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <span itemprop="name"><?php echo $arianetitle; ?></span>
                <meta itemprop="position" content="2" />
        </li>
        <?php
        break;
        }
        case 'level1':
        {
        ?>
        &gt
        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <span itemprop="name"><?php echo $arianetitle; ?></span>
                <meta itemprop="position" content="2" />
        </li>
        <?php
        break;
        }
        case 'level2':
        {
        ?>
        &gt
        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a itemprop="item" href="<?php echo $domain; ?>/<?php echo $parent_arianelink; ?>">
                <span itemprop="name"><?php echo $parent_arianetitle; ?></span>
            </a>
            <meta itemprop="position" content="2" />
        </li>
        &gt
        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <span itemprop="name"><?php echo $arianetitle; ?></span>
                <meta itemprop="position" content="3" />
        </li>
        <?php
        break;
        }
        case 'composit':
        {
        ?>
        &gt
        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a itemprop="item" href="<?php echo $domain; ?>/<?php echo $oldest_parent_arianelink; ?>">
                <span itemprop="name"><?php echo $oldest_parent_arianetitle; ?></span>
            </a>
                <meta itemprop="position" content="2" />
        </li>
        &gt
        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a itemprop="item" href="<?php echo $domain; ?>/<?php echo $older_parent_arianelink; ?>">
                <span itemprop="name"><?php echo $older_parent_arianetitle; ?></span>
            </a>
                <meta itemprop="position" content="3" />
        </li>
        &gt
        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a itemprop="item" href="<?php echo $domain; ?>/<?php echo $parent_arianelink; ?>">
                <span itemprop="name"><?php echo $parent_arianetitle; ?></span>
            </a>
                <meta itemprop="position" content="4" />
        </li>
        &gt
        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <span itemprop="name"><?php echo $arianetitle; ?></span>
                <meta itemprop="position" content="5" />
        </li>
        <?php
        break;
        }
        case 'search':
        {
        ?>
        &gt
        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <span itemprop="name"><?php echo $arianetitle; ?></span>
                <meta itemprop="position" content="2" />
        </li>
        <?php
        break;
        }
        // BLOG
        case 'blog':
        {

            switch ($arianefile)
            {
                case 'home':
                {
                ?>
                &gt
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <span itemprop="name"><?php echo $arianetitle; ?></span>
                        <meta itemprop="position" content="2" />
                </li>
                <?php
                break;
                }
                case 'advice':
                {
                ?>
                &gt
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="<?php echo $domain; ?>/<?php echo $blog; ?>/">
                        <span itemprop="name"><?php echo $blog_arianetitle; ?></span>
                    </a>
                    <meta itemprop="position" content="2" />
                </li>
                &gt
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <span itemprop="name"><?php echo $arianetitle; ?></span>
                        <meta itemprop="position" content="3" />
                </li>
                <?php
                break;
                }
                case 'guide':
                {
                ?>
                &gt
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="<?php echo $domain; ?>/<?php echo $blog; ?>/">
                        <span itemprop="name"><?php echo $blog_arianetitle; ?></span>
                    </a>
                    <meta itemprop="position" content="2" />
                </li>
                &gt
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <span itemprop="name"><?php echo $arianetitle; ?></span>
                        <meta itemprop="position" content="3" />
                </li>
                <?php
                break;
                }
                case 'article':
                {
                ?>
                &gt
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="<?php echo $domain; ?>/<?php echo $blog; ?>/">
                        <span itemprop="name"><?php echo $blog_arianetitle; ?></span>
                    </a>
                    <meta itemprop="position" content="2" />
                </li>
                &gt
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <span itemprop="name"><?php echo $arianetitle; ?></span>
                        <meta itemprop="position" content="3" />
                </li>
                <?php
                break;
                }
                case 'gamme':
                {
                ?>
                &gt
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="<?php echo $domain; ?>/<?php echo $blog; ?>/">
                        <span itemprop="name"><?php echo $blog_arianetitle; ?></span>
                    </a>
                    <meta itemprop="position" content="2" />
                </li>
                &gt
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="<?php echo $domain; ?>/<?php echo $blog; ?>/<?php echo $parent_arianelink; ?>">
                        <span itemprop="name"><?php echo $parent_arianetitle; ?></span>
                    </a>
                    <meta itemprop="position" content="3" />
                </li>
                &gt
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <span itemprop="name"><?php echo $arianetitle; ?></span>
                        <meta itemprop="position" content="4" />
                </li>
                <?php
                break;
                }
                case 'constructeurs':
                {
                ?>
                &gt
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="<?php echo $domain; ?>/<?php echo $blog; ?>/">
                        <span itemprop="name"><?php echo $blog_arianetitle; ?></span>
                    </a>
                    <meta itemprop="position" content="2" />
                </li>
                &gt
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <span itemprop="name"><?php echo $arianetitle; ?></span>
                        <meta itemprop="position" content="3" />
                </li>
                <?php
                break;
                }
                case 'marque':
                {
                ?>
                &gt
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="<?php echo $domain; ?>/<?php echo $blog; ?>/">
                        <span itemprop="name"><?php echo $blog_arianetitle; ?></span>
                    </a>
                    <meta itemprop="position" content="2" />
                </li>
                &gt
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="<?php echo $domain; ?>/<?php echo $blog; ?>/<?php echo $parent_arianelink; ?>">
                        <span itemprop="name"><?php echo $parent_arianetitle; ?></span>
                    </a>
                    <meta itemprop="position" content="3" />
                </li>
                &gt
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <span itemprop="name"><?php echo $arianetitle; ?></span>
                        <meta itemprop="position" content="4" />
                </li>
                <?php
                break;
                }
                case 'concept':
                {
                ?>
                &gt
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="<?php echo $domain; ?>/<?php echo $blog; ?>/">
                        <span itemprop="name"><?php echo $blog_arianetitle; ?></span>
                    </a>
                    <meta itemprop="position" content="2" />
                </li>
                &gt
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="<?php echo $domain; ?>/<?php echo $blog; ?>/<?php echo $first_parent_arianelink; ?>">
                        <span itemprop="name"><?php echo $first_parent_arianetitle; ?></span>
                    </a>
                    <meta itemprop="position" content="3" />
                </li>
                &gt
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="<?php echo $domain; ?>/<?php echo $blog; ?>/<?php echo $first_parent_arianelink; ?>/<?php echo $parent_arianelink; ?>">
                        <span itemprop="name"><?php echo $parent_arianetitle; ?></span>
                    </a>
                    <meta itemprop="position" content="3" />
                </li>
                &gt
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <span itemprop="name"><?php echo $arianetitle; ?></span>
                        <meta itemprop="position" content="4" />
                </li>
                <?php
                break;
                }
                case 'guide.item':
                {
                ?>
                &gt
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="<?php echo $domain; ?>/<?php echo $blog; ?>/">
                        <span itemprop="name"><?php echo $blog_arianetitle; ?></span>
                    </a>
                    <meta itemprop="position" content="2" />
                </li>
                &gt
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="<?php echo $domain; ?>/<?php echo $blog; ?>/<?php echo $parent_arianelink; ?>">
                        <span itemprop="name"><?php echo $parent_arianetitle; ?></span>
                    </a>
                    <meta itemprop="position" content="3" />
                </li>
                &gt
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <span itemprop="name"><?php echo $arianetitle; ?></span>
                        <meta itemprop="position" content="4" />
                </li>
                <?php
                break;
                }
            }

        break;
        }
    }
    ?>
</ol>