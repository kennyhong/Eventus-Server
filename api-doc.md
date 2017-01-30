
# Events

#### Add Event  
HTTP | route | Description:  
-- | -- | --  
**POST** | `api/events` | Create a new Event.

#### Get All Events
HTTP | route | Description:  
-- | -- | --  
**GET** | `api/events` | List all Events.

#### Delete a specified Event  
HTTP | route | Description:  
-- | -- | --  
**DELETE** | `api/events/{event}` | Delete an Event by id, e.g. `api/events/2`

#### Retrieve a specified Event  
HTTP | route | Description:  
-- | -- | --  
**GET** | `api/events/{event}` | Retrieve an Event by id, e.g. `api/events/2`

#### Update a specified Event  
HTTP | route | Description:  
-- | -- | --  
**PUT, PATCH** | `api/events/{event}` | Update an Event by id, e.g. `api/events/2`

#### Get all Service items for specified Event
HTTP | route | Description:  
-- | -- | --  
**GET** | `api/events/{event}/services` | Retrieve all Services for an Event by id, e.g. `api/events/2/services`

#### Remove a Service from an Event
HTTP | route | Description:  
-- | -- | --  
**DELETE** | `api/events/{event}/services/{service}` | Remove the specified Service from the specified Event, e.g. `api/events/2/services/1` removes Service 1 from Event 2.

#### Add a Service to an Event
HTTP | route | Description:  
-- | -- | --  
**POST** | `api/events/{event}/services/{service}` | Add the specified Service to the specified Event, e.g. `api/events/2/services/1` adds Service 1 to Event 2.



# ServiceTags

#### Get all ServiceTags
HTTP | route | Description:  
-- | -- | --  
**GET** | `api/service_tags` | List all of the ServiceTags.

#### Get a specified ServiceTag
HTTP | route | Description:  
-- | -- | --  
**GET** | `api/service_tags/{service_tag}` | Get a specified ServiceTag by id, e.g. `api/service_tags/2`



# Services

#### Get all Services
HTTP | route | Description:  
-- | -- | --  
**GET** | `api/services` | List all of the Services.

#### Get a specified Service
HTTP | route | Description:  
-- | -- | --  
**GET** | `api/services/{service}` | Get a specified Service by id, e.g. `api/services/2`

#### Get all ServiceTag items for a specified Service
HTTP | route | Description:  
-- | -- | --  
**GET** | `api/services/{service}/service_tags` | Retrieve all ServiceTags for a Service by id, e.g. `api/services/2/service_tags`

#### Remove a ServiceTag from a Service
HTTP | route | Description:  
-- | -- | --  
**DELETE** | `api/services/{service}/service_tags/{service_tag}` | Remove the specified ServiceTag from the specified Service, e.g. `api/services/2/service_tags/1` removes ServiceTag 1 from Service 2.

#### Add a ServiceTag to a Service
HTTP | route | Description:  
-- | -- | --  
**POST** | `api/services/{service}/service_tags/{service_tag}` | Add the specified ServiceTag to the specified Service, e.g. `api/services/2/service_tags/1` adds ServiceTag 1 to Service 2.
