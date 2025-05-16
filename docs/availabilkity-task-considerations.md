# Availability Task Considerations

## Requirements Analysis

The goal of the task is to provide an API endpoint that allows its consumers to fetch a list of available cars at a certain station for a certain time period including pricing information by car.

### Availability

There are no indicators on any business constraints towards the availability calculation. Potential constraints from my own experience of car renting could be:
- pick up times potentially needing to be taken into account, e.g. no pickup out of working hours or on Sundays or Bank Holidays, but return *might* be possible (maybe depending on the station)
- "down time" of a car between being returned and picked up by the next renter, e.g. for cleaning purposes
- how fix are cars bound to a station? To accommodate a high demand of cars at station x, it *could* be a business practice to shift cars over to it from nearby stations when the station is running out of cars to rent out. Also, the API discussed in the existing PR only allows pickup and return station to be the same, there are services allowing them to be different.
- the existing codebase contains a concept of booking statuses and car activity, the latter is not addressed at all in the example PR, I will filter out inactive cars besides blocking booking statuses

A car therefore is considered "available" between days for now by:
- the car being allocated to the accoding station
- no booking overlapping during the whole period by *day*, ignoring hours
- "rejected" and "cancelled" bookings should be ignored when calculating (in)availability

As without peering with a domain expert it is hard to tell what constraints will be needed to be implemented, I will keep it simple, but might sketch in example filters.


### Pricing

The pricing information is accessible via a service that can be called via an API call. Prices can be different between ~~models~~ cars and timeframes according to the example PR.

We do have control over that pricing service though as well and could move logic over to that service and adjust its endpoints to fit the applications needs better than in the existing PR.

Flaws identified and to be improved in my opinion are (maybe not a fully complete list here):
- should prices have a currency? this would lead to a bigger adjustment to the whole application and therefore will be left out of scope.
- it should be avoided to call the API for each car individually for performance reasons
- in a real life example, I would be surprised if prices wouldn't also depend on the station as typically prices are based on supply and demand and that's definitely different per region (even within a city for normal car renting, e.g. close to the airport or inner city versus in the outskirts)
- the pricing API is expecting the car id of our application which is an auto incremented int according to the entity attribute. This feels a bit weird that our system is the primary of artificial identifiers, it might be better for stability to come up with an alternative.
- also, the pricing API is not based on car *models* but individual cars, which again feels kind of uncommon for how I experienced car rental myself. I will *not* aim to change that now as business domain topics are getting more and more, but would strongly suggest evaluating this in real life before heading into implementation. 
- the existing PR implies that prices can be cached. the likelihod that different users are aiming to use the exact same time period for the same station (which is missing in the existing PR, but considered a business expectation rather sooner than later) is unknown. The hit-miss-ratio of the cache might be pretty awful, making the cache a cost factor of storing temporary data without the assumed positive impact to the user experience. Also, prices might be a pretty dynamic thing to the business, e.g. by considering higher demands during vacation days, bookings made on certain models, etc. While a http request is considered "expensive" I would consider it best practice to leave performance optimization like caching to the pricing service itself for now, having the imaginary control over it, before looking into optimizing it on our end as well.

I will try to reflect those aspects in my solution, but potentially not implement its details due to time constraints.


### Premium

The API is expected to expose the information whether a car is only available to book as a premium user. This depends on the car model and the calculated price being over a threshold (per model type) according to the existing PR.

I expect the "premium user" validation etc in the booking service being far out of scope.

We could reason that the premium logic solely based on car model and price could be moved over to the pricing endpoint. But as there might be further criteria added that is out of my visibility, I would rather keep it within our application.

The example PR hardcoded the models and price threshold, I would suggest to move it into the database to allow adjustments without a need of a deployment or config change at minimum. To properly limit unexpected side effects this could imply to move the model string in the cars table into its own models table introducing foreign keys, once more, domain design topics are piling up before even looking into the actual coding solution. Also, with different currencies, there's not *one* threshold to configure per model, but multiple. Adding all that logic feels a bit heavy to demonstrate my coding skills though...


## Existing Architecture

The architecture follows the Symfony default's flavour of a folder structure focussing on the tech aspect (Controller, Entity, ...) more than the domain (Booking, Station, ...). 

The existing application uses a pattern of "Requests" (not http, but application layer) to pass to "Service" classes (quite generic naming, I personally would recommend aiming for ubiquotous language or "DDD flavour" here, but as that is how the application has been started, I will try to keep up with the same convention) and using ViewModels to expose data back to the "outside". Also, there is no bounded context

I personally favour a bit more visible separation of layers, by folder structure or other concepts used. I would e.g. strongly recommend to lean towards ports and adapters *thinking* of the Hexagonal Architecture concept by at least introducing interfaces instead of concrete implementations between those layers, in this case here especially the infrastructure related classes like Repositories.


## Aimed approach

The existing PR is implying a few details on the availability API endpoint structure (uri, using query parameters over route parameters, ...) which I will not touch but rather lean towards (while one could e.g. argue that /station/:station_id/availabilities might be a better API uri choice).

The "availability calculation" is the core aspect of the solution and therefore should be implemented robust and well tested.

As mentioned above I will stick to most concepts established in the main branch, but might make slight adjustments. I will keep the adjustments to the task at hand, not aiming for consistency and apply those changes to the existing endpoints but would suggest and discuss doing so in a real life scenario before.

Depending on aaaaall the business domain considerations, due to time constraints I will *not* implement them all.