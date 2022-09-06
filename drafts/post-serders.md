title: Replace default jdk serialization to json for spring security
lang: en
description: Replace default jdk serialization to json for spring security
keywords: Spring Boot, OAuth, security, jdk serialization, json serialization, jackson
---

<h3>Problem and initial description</h3>
There was a task related to upgrading spring boot version from 2.2.1 to 2.3.9. After upgrade I saw a lot of messaging in log system related to invalid token. It was well known issue related to jdk serialization.
```
java.lang.IllegalArgumentException: java.io.InvalidClassException: org.springframework.security.core.authority.SimpleGrantedAuthority; local class incompatible: stream classdesc serialVersionUID = 520, local class serialVersionUID = 530
```

In short spring-sceurity uses Jdk serialization/deserialization by default. Each class has serialVersionUID and if you serialize your object with one version and try to deserilize with another you will get the IllegalArgumentException error because serialVersionUID is different. And you will get such error each time after upgrading spring. Not sure if it is related to minor version but it is true for 2.2.x-> 2.3.x. So Spring Security is not intended to be serialized between versions and this was pain for a lot of people.

I leave some links here:
1. https://stackoverflow.com/questions/1438733/java-serialization-problem
2. https://github.com/spring-projects/spring-security/issues/1945
3. https://github.com/spring-projects/spring-security/issues/9204


<h3>Idea</h3>
Idea is similar to described [here](https://github.com/spring-projects/spring-security/issues/3736). I mean replace jsk serialization to json serialization and avoid serialVersionUID checks.


<h3>Finding working solution</h3>
I did not find any out-of-the-box solution but found [an article](https://www.programmersought.com/article/6321627776/). The article describes an approach and provide samples of the code. I borrowed code and made changes related to token expiration and got expected result.

<h3>What I expected for</h3>
I used Jdbc store for my tokens: access and refresh. So I expected json version in oauth_access_token and oauth_refresh_token tables instead of byte array. I expected to retrieve token without any issues and expiration time should be correct (I mean will be decreased each time I retrieve and retrieve new one if expired).

So let me show my code and leave some notes here for explanation.