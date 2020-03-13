title: Wordpress. Migrating between environments such as development, staging and prod
lang: en
description: How to migrate wordpress between different environments (dev, staging, production)
keywords: wordpress, merge database, migrate between environments, WPMerge, WP MIGRATE DB PRO, WP Sync DB, WP Staging PRO 
---
## Requirements and task definition

We have a separate install of wordpress in each environment. We’ll need an easy way to sync content between them, through the wordpress GUI (so that this can be done by nontechnical admins).
Prove out migrating content between installs. There’s no need to be able to integrate multiple versions or resolve conflicts, but if it’s possible to detect a conflict that would be nice.
This should be able to be bi-directional (from eg prod environment into dev or from dev back into prod).
More detailed explanation can be found in sitepoint article (https://www.sitepoint.com/synchronize-wordpress-live-development-databases/)

## Candidates
I filtered candidates by reading description and list below is more relevant to my task. I decided to not to 
hide other candidates and placed it to Out of scope list.
  
1. [WPMerge](https://wpmerge.io/)
2. [WP MIGRATE DB PRO](https://deliciousbrains.com/wp-migrate-db-pro/)
3. [WP Sync DB](http://wp-sync-db.github.io/)
4. [WPSiteSync for Content](https://wordpress.org/plugins/wpsitesynccontent/) + https://wpsitesync.com/ as a premium.
5. [WP Staging](https://wordpress.org/plugins/wp-staging/) + Pro version
6. [UpdraftPlus Migrator](https://updraftplus.com/migrator/)

### Out of scope
* [VersionPress](https://versionpress.net/) - The product is in development but seems promising.
* [Database Sync](https://wordpress.org/plugins/database-sync/) - WARNING: This plugin is for advanced users. If used incorrectly it could wipe out all your content!
* [WordPress Importer](https://wordpress.org/plugins/wordpress-importer/) - manual process, only import and there is no bi directional features.
* [SyncDB](https://github.com/jplew/SyncDB) - bash script is not user friendly.
* [WordPress GitHub Sync](https://wordpress.org/plugins/wp-github-sync/) - This could be an interesting option for teams requiring content editing collaboration and pull request approval workflows.
* [PushLive – Staging Sites to Live in One Click](https://wordpress.org/plugins/pushlive/) - Seems interesting but looks like no longer supported or mainteined. There is no pull feature.
* [WP Stagecoach](https://wpstagecoach.com/pricing/) - external service. More expensive than others. Looks good but not sure about bo directional feature.
* All of MySQL Synchronization Tools or not user friendly.


## Comparison table

| Product                | Price, $ | BiDirectional Sync        | Trial | Notice                                                |
|------------------------|----------|---------------------------|-------|-------------------------------------------------------|
| WPMerge                | 147/site | Yes                       | No    | Refund for 14 days. https://youtu.be/lEnGhHa6f1c?t=92 |
| WP MIGRATE DB PRO      | 99/site  | Yes                       | No    | Refund for 60 days. https://youtu.be/8u_kX5d78Bs      |
| WP Sync DB             | 0        | Yes                       | N/A   |                                                       |
| WP Staging PRO         | 96       | Yes                       | No    | Refund for 60 days. https://youtu.be/V9zkyluQJp4      |
| WPSiteSync for Content | N/A      | N/A                       | N/A   | Could not open site, seems unreachable from Russia.   |
| UpdraftPlus Migrator   | 30/70    | Yes via manual restoring. | No    |                                                       |

All of plugins does not support trial version or demo. Seems that WP Staging PRO suits better. But the difference is only 3$.
I am not sure I need media files sync function. I use plugin that stores media content in amazon s3 buckets. 
So I need a tool to sync content between buckets and look at was sync utility. Media content stores in wordpress as a link.
I used WP Sync DB as a trial version of WP MIGRATE DB PRO and it seems working. I am happy to suggest it to my customer.
WP MIGRATE DB PRO makes sense to me due to support and updates. So my choice is WP MIGRATE DB PRO for business and
WP Sync DB for personal usage.
