import pymysql
import json
import requests
import time

start = time.time()
counter = 0

response = requests.get("http://aviation-edge.com/v2/public/flights?key=a1cfea-a5248d")
data = json.loads(response.text)
connection = pymysql.connect(host="localhost", user="root", passwd="", database="flighttrackinator3000")
cursor = connection.cursor()

cursor.execute("DELETE FROM flight")

for flight in data:
    if flight['status'] == "en-route":
        aircraft_id = flight['aircraft']['regNumber']
        altitude = round(flight['geography']['altitude'] * 3.28084, 1)    # converting from m to ft
        latitude = flight['geography']['latitude']
        longitude = flight['geography']['longitude']
        direction = flight['geography']['direction']
        speed = round(flight['speed']['horizontal'] * 0.621371, 1)        # converting from km/h to mph
        airline = flight['airline']['iataCode']
        flight_num = flight['flight']['number']
        aircraft_icao = flight['aircraft']['icaoCode']
        source = flight['departure']['iataCode']
        destination = flight['arrival']['iataCode']

        if airline == "" or aircraft_icao == "" or source == "" or destination == "":
            continue    # skips over any flights with non existing values

        print("Airline:", flight['airline']['iataCode'], "- Flight:", flight['flight']['number'], "::", flight['departure']['iataCode'], "->", flight['arrival']['iataCode'])
        # print("GEO: ", flight['geography'])
        # print("SPD: ", flight['speed'])

        sql = "INSERT INTO flight(aircraft_id, altitude, latitude, longitude, direction, speed, airline, flight_num, aircraft_icao, source, destination) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s);"
        cursor.execute(sql, (aircraft_id, altitude, latitude, longitude, direction, speed, airline, flight_num, aircraft_icao, source, destination))

        connection.commit()
        counter += 1

connection.close()

print("\nTracking " + str(counter) + " flights.")
print("Finished in " + str(round((time.time() - start), 1)) + " seconds.")
