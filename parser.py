import pymysql
import json
import requests

response = requests.get("http://aviation-edge.com/v2/public/flights?key=7d8f71-1d01ce")
data = json.loads(response.text)
connection = pymysql.connect(host="localhost", user="root", passwd="", database="flighttrackinator3000")
cursor = connection.cursor()

cursor.execute("DELETE FROM flight")

for flight in data:
    if flight['status'] == "en-route":
        print("Airline:", flight['airline']['iataCode'], "- Flight:", flight['flight']['number'], "::", flight['departure']['iataCode'], "->", flight['arrival']['iataCode'])
        # print("GEO: ", flight['geography'])
        # print("SPD: ", flight['speed'])

        aircraft_id = flight['aircraft']['regNumber']
        altitude = flight['geography']['altitude']
        latitude = flight['geography']['latitude']
        longitude = flight['geography']['latitude']
        direction = flight['geography']['direction']
        speed = flight['speed']['horizontal']
        airline = flight['airline']['iataCode']
        model = flight['aircraft']['iataCode']
        source = flight['departure']['iataCode']
        destination = flight['arrival']['iataCode']
        # print("INSERTING TO DB: ", lat, lon, direction, altitude, '\n')

        sql = "INSERT INTO flight(aircraft_id, altitude, latitude, longitude, direction, speed, airline, model, source, destination) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s);"
        cursor.execute(sql, (aircraft_id, altitude, latitude, longitude, direction, speed, airline, model, source, destination))
        connection.commit()

connection.close()






    # for adsbexchange API
    # if(flight['Id'] == 8190193):
    #     print(flight["Alt"])
#API KEY FOR Aviation Edge: 7d8f71-1d01ce