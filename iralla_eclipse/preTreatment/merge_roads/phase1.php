<?php
/*
 * 
 * 	In this phase all at each the vertex of each "other" road, will look for 
 * near buslines.
 * 
 */



/*
 * find all roads "other" from database
 */




/*
 * from each road "other"
 */




/*
 * 		for each vertex
 */



/*
 * 			make 4 segments orthogonales of the two segments joints at the vertex
 * 			with the vertex at one end
 * 			with a length of $interval x 2
 */
			

/*
 * 			for each segment	
 */
			


/*
 * 				find the intersections points with the other roads of "other" type:
 */


/*
 * 				save the intersection points in a VertexAndIntersection class
 * 				save the vertexAndIntersection depending of the segment from
 * 				and if its in first half or the second half of the segment
 * 				if the segment is completly mixed up with the bus line,
 * 				the intersectin point will be the vertex of the current bus line and save the
 * 				vertexAndIntersection inside the allVertexAndIntersection
 * 				if the segment is partly mixed up with the bus line,
 * 				the intersectin point will be the vertex of the current bus line if 
 * 				crossed or the vertex of the other bus line in the segment
 */
 


/*
 * 		end for each vertex
 * 
 * 		save all the vertexAndIntersection in the same array allVertexAndIntersection
 * 		with these rules:
 * 			-the vertexAndIntersection found in the secund half will be saved 
 * 			only if at leat one vertexAndIntersection was found in the first half
 * 			-all the vertexAndIntersection found in the first half are saved
 */
 

/*
 * end from each road "other"
 * 
 * save all the vertexAndIntersection in the data base
 * 
 */