# Trustpilot plugin for Craft CMS 3.x

## Requirements

This plugin requires Craft CMS 3.1.0 or later.

## Before you begin

The Trustpilot plugin requires a Trustpilot business account that is signed up to the Standard plan, with the Connect module added.

## Installing the Trustpilot plugin

To install the plugin, follow these instructions.

1.  Open your terminal and go to your Craft project:

        cd /path/to/project

2.  Then tell Composer to load the plugin:

        composer require scaramangagency/trustpilot

3.  In the Control Panel, go to Settings → Plugins and click the “Install” button for Trustpilot.

You can also visit the [Craft Plugin Store](https://plugins.craftcms.com/), search for _Trustpilot_ and click install.

## Configuring Trustpilot

### Assigning user permissions

The _Trustpilot_ plugin offers granular user permission, which you must add to existing and new user groups.

Define whether a user and/or user group has permission to:

-   Manage settings
-   View reviews
-   Manage comments on reviews
-   Send invitations

### Plugin settings

The following options are available under the _Settings_ tab:

-   **Trustpilot username** &mdash; Your Trustpilot username.
-   **Trustpilot password** &mdash; Your Trustpilot password.
-   **Trustpilot API Key** &mdash; Your Trustpilot password.
-   **Trustpilot API Secret** &mdash; Your Trustpilot password.

You can learn how to create your API key and API secret by visiting Trustpilot's [own documentation](https://support.trustpilot.com/hc/en-us/articles/207309867-Introduction-to-Trustpilot-APIs#create-api-application-2)

-   **Trustpilot website URL** &mdash; The website URL attached to your Trustpilot account.
-   **Invitation Sender Email** &mdash; The email you would like Trustpilot invitations to be sent from.
-   **Invitation Sender Name** &mdash; The name you would like Trustpilot invitations to be sent from.
-   **Invitation Reply-to** &mdash; The email address that responses from the invitee should be sent to.

All values can be set to environment variables.

## Using Trustpilot in the Control Panel

### Invitations

#### Send Invitation

Send an invitation to a customer. Enter their email and name (and optionally a redirect URL, if you wanted to send them to a custom thanks page) and hit ‘Send Invitation’. This will tender an email to be sent within one hour.

You can check that reviews have been tendered by going into your Business account dashboard, and visiting Get Revirws > Overview > See full invitation historu.

#### Generate Review Link

Enter a customer name and email, then click generate to get an invitation link. You can then send the link onto your customer manually.

### Reviews

Displays all reviews in your Trustpilot account. You can click ‘Add reply’ to add a reply to the review. If you have already added a reply, you can click ‘Edit Reply’ instead, which lets you edit or delete your existing reply. 

## Using Trustpilot in your templates

There are a variety of variables you can use in your templates.  

### getAllReviews

Return all reviews.

```
craft.trustpilot.getAllReviews(page, limit, siteId)
```
| Field Name | Description |
| --- | --- |
| page | When paginating, insert the page you want to retrieve |
| limit | For paginating (or just limiting your result set), select how many reviews to pull from the API |
| siteId | The current site's ID. If you only have one site on your install you can leave this blank, otherwise pass the `siteId` for the site you have added the authorisation to. You can hard-code the site ID if you have only set up authorisation on one of your multi-site installs, otherwise pass the current `siteId` dynamically |

#### Response

```
{ 
    "author" => [
        "name" => "Scaramanga Agency",
        "previousReviewCount" => 2
    ],
    "reviewStars" => [
        "count" => 3,
        "image" => "https://api.trustpilot.com/v1/resources/images/stars/3"
    ],
    "reviewTitle" => "Lorem Ipsum Dolor Sit Amet",
    "reviewText" => "Lorem Ipsum Dolor Sit Amet",
    "reviewPosted" => "2021-05-20T09:55:56Z",
    "companyReply" => "Lorem Ipsum Dolor Sit Amet"
}
```

### getProfile

Get your basic profile information.

```
craft.trustpilot.getProfile(siteId)
```
| Field Name | Description |
| --- | --- |
| siteId | The current site's ID. If you only have one site on your install you can leave this blank, otherwise pass the `siteId` for the site you have added the authorisation to. You can hard-code the site ID if you have only set up authorisation on one of your multi-site installs, otherwise pass the current `siteId` dynamically |

#### Response

```
{ 
    "websiteUrl" => ""
    "displayName" => "Lorem Ipsum Dolor Sit Amet",
    "trustScore" => 3.6,
    "stars" => 3.5,
    "count" => [
        "total" => 2,
        "oneStar" => 0,
        "twoStars" => 0,
        "threeStars" => 1,
        "fourStars" => 1,
        "fiveStars" => 0
    ]
}
```

### getTrustpilotStarImage

Get star images for the Trustpilot account set up on the supplied site ID

```
craft.trustpilot.getTrustpilotStarImage(siteId)
```
| Field Name | Description |
| --- | --- |
| siteId | The current site's ID. If you only have one site on your install you can leave this blank, otherwise pass the `siteId` for the site you have added the authorisation to. You can hard-code the site ID if you have only set up authorisation on one of your multi-site installs, otherwise pass the current `siteId` dynamically |

#### Response

```
{ 
    "star128x24" => [
        "width" => 128,
        "height" => 24,
        "url" => "//images-static.trustpilot.com/api/stars/3.5/128x24.png"
    ], 
    "star256x48" => [
        "width" => 256,
        "height" => 48,
        "url" => "//images-static.trustpilot.com/api/stars/3.5/256x48.png"
    ],
    "star512x96" => [
        "width" => 512,
        "height" => 96,
        "url" => "//images-static.trustpilot.com/api/stars/3.5/512x96.png"
    ],
    "starSvg" => [
        "width" => 128,
        "height" => 24,
        "url" => "//images-static.trustpilot.com/api/stars/3.5/star.svg"
    ],
    "stars" => 3.5
}
```

### getTrustpilotStarText

Get textual representation of the star rating for the Trustpilot account set up on the supplied site ID

```
craft.trustpilot.getTrustpilotStarText(siteId)
```
| Field Name | Description |
| --- | --- |
| siteId | The current site's ID. If you only have one site on your install you can leave this blank, otherwise pass the `siteId` for the site you have added the authorisation to. You can hard-code the site ID if you have only set up authorisation on one of your multi-site installs, otherwise pass the current `siteId` dynamically |

#### Response

```
{
    "locale": "en-US",
    "string": "Good",
    "stars": 4
}
```

### getTrustpilotPermalinks

Get the permalinks (profile URL, embed URL) for the Trustpilot account set up on the supplied site ID

```
craft.trustpilot.getTrustpilotPermalinks(siteId)
```
| Field Name | Description |
| --- | --- |
| siteId | The current site's ID. If you only have one site on your install you can leave this blank, otherwise pass the `siteId` for the site you have added the authorisation to. You can hard-code the site ID if you have only set up authorisation on one of your multi-site installs, otherwise pass the current `siteId` dynamically |

#### Response

```
{
    "profileUrl"=> "https://www.trustpilot.com/review/XX", 
    "evaluateEmbedUrl" => "https://www.trustpilot.com/evaluate/embed/XX",
    "evaluateUrl"=> "https://www.trustpilot.com/evaluate/XX"
}
```

### getTrustpilotIcons

Pull the Trustpilot icon resources

```
craft.trustpilot.getTrustpilotIcons()
```

#### Response

```
{
    "icon230x230" => [
        "width" => 230,
        "height" => 230,
        "url" => "//images-static.trustpilot.com/api/icons/230x230.png"
    ]
}
```

### getTrustpilotLogos

Pull the Trustpilot logo resources

```
craft.trustpilot.getTrustpilotLogos()
```

#### Response

```
{
    "darkBackground": {
        "logo72x18": {
            "url": "//trustpilot.com/image.jpg",
            "width": "400",
            "height": "300"
        },
        "logo144x36": {
            "url": "//trustpilot.com/image.jpg",
            "width": "400",
            "height": "300"
        },
        "logo288x72": {
            "url": "//trustpilot.com/image.jpg",
            "width": "400",
            "height": "300"
        },
        "logo576x144": {
            "url": "//trustpilot.com/image.jpg",
            "width": "400",
            "height": "300"
        },
        "logoSvg": {
            "url": "//trustpilot.com/image.jpg",
            "width": "400",
            "height": "300"
        }
    },
    "lightBackground": {
        "logo72x18": {
            "url": "//trustpilot.com/image.jpg",
            "width": "400",
            "height": "300"
        },
        "logo144x36": {
            "url": "//trustpilot.com/image.jpg",
            "width": "400",
            "height": "300"
        },
        "logo288x72": {
            "url": "//trustpilot.com/image.jpg",
            "width": "400",
            "height": "300"
        },
        "logo576x144": {
            "url": "//trustpilot.com/image.jpg",
            "width": "400",
            "height": "300"
        },
        "logoSvg": {
            "url": "//trustpilot.com/image.jpg",
            "width": "400",
            "height": "300"
        }
    }
}
```

---

Brought to you by [Scaramanga Agency](https://scaramanga.agency)
