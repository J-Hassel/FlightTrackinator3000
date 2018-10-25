import json
import requests

response = requests.get("http://aviation-edge.com/v2/public/flights?key=7d8f71-1d01ce&depIata=MIA")
data = json.loads(response.text)

for flight in data:
    print("Airline:", flight['airline']['iataCode'], "- Flight:", flight['flight']['number'], "::", flight['departure']['iataCode'], "->", flight['arrival']['iataCode'])
    print("GEO: ", flight['geography'])
    print("SPD: ", flight['speed'], '\n')



    # for adsbexchange API
    # if(flight['Id'] == 8190193):
    #     print(flight["Alt"])


#API KEY FOR Aviation Edge: 7d8f71-1d01ce